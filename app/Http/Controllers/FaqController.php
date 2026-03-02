<?php

namespace App\Http\Controllers;

use App\Models\FaqArticle;
use App\Models\FaqCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;

class FaqController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/support/faq",
     *     tags={"Support"},
     *     summary="Get all FAQ categories with articles",
     *     description="Returns all active FAQ categories with their articles",
     *     @OA\Response(
     *         response=200,
     *         description="FAQ data retrieved successfully"
     *     )
     * )
     */
    public function index(): JsonResponse
    {
        try {
            $categories = FaqCategory::active()
                ->with(['activeArticles'])
                ->get()
                ->map(function ($category) {
                    return [
                        'id' => $category->slug,
                        'icon' => $category->icon,
                        'label' => $category->label,
                        'desc' => $category->description,
                        'color' => $category->color,
                        'article_count' => $category->activeArticles->count(),
                        'articles' => $category->activeArticles->map(function ($article) {
                            return [
                                'id' => $article->id,
                                'q' => $article->question,
                                'a' => $article->answer,
                                'helpful_count' => $article->helpful_count,
                                'helpfulness' => $article->helpfulness_ratio,
                            ];
                        }),
                    ];
                });

            return response()->json([
                'status' => 'success',
                'categories' => $categories,
            ], 200);
        } catch (Exception $e) {
            Log::error('Failed to fetch FAQ: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'error' => 'Failed to fetch FAQ data',
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/support/faq/search",
     *     tags={"Support"},
     *     summary="Search FAQ articles",
     *     description="Search across all FAQ articles by keyword",
     *     @OA\Parameter(
     *         name="q",
     *         in="query",
     *         required=true,
     *         description="Search query",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Search results"
     *     )
     * )
     */
    public function search(Request $request): JsonResponse
    {
        try {
            $query = $request->input('q', '');

            if (strlen($query) < 2) {
                return response()->json([
                    'status' => 'success',
                    'query' => $query,
                    'results' => [],
                ], 200);
            }

            $results = FaqArticle::active()
                ->search($query)
                ->with('category')
                ->limit(20)
                ->get()
                ->map(function ($article) {
                    return [
                        'id' => $article->id,
                        'q' => $article->question,
                        'a' => $article->answer,
                        'category' => [
                            'id' => $article->category->slug,
                            'icon' => $article->category->icon,
                            'label' => $article->category->label,
                            'color' => $article->category->color,
                        ],
                    ];
                });

            return response()->json([
                'status' => 'success',
                'query' => $query,
                'count' => $results->count(),
                'results' => $results,
            ], 200);
        } catch (Exception $e) {
            Log::error('FAQ search failed: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'error' => 'Search failed',
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/support/faq/{categorySlug}",
     *     tags={"Support"},
     *     summary="Get FAQ category with articles",
     *     description="Returns a specific FAQ category with all its articles",
     *     @OA\Parameter(
     *         name="categorySlug",
     *         in="path",
     *         required=true,
     *         description="Category slug (e.g., account, groups, payments)",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Category data retrieved"
     *     )
     * )
     */
    public function category(string $categorySlug): JsonResponse
    {
        try {
            $category = FaqCategory::where('slug', $categorySlug)
                ->where('is_active', true)
                ->with('activeArticles')
                ->first();

            if (!$category) {
                return response()->json([
                    'status' => 'error',
                    'error' => 'Category not found',
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'category' => [
                    'id' => $category->slug,
                    'icon' => $category->icon,
                    'label' => $category->label,
                    'desc' => $category->description,
                    'color' => $category->color,
                    'articles' => $category->activeArticles->map(function ($article) {
                        return [
                            'id' => $article->id,
                            'q' => $article->question,
                            'a' => $article->answer,
                            'helpful_count' => $article->helpful_count,
                            'helpfulness' => $article->helpfulness_ratio,
                        ];
                    }),
                ],
            ], 200);
        } catch (Exception $e) {
            Log::error('Failed to fetch category: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'error' => 'Failed to fetch category',
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/support/faq/{articleId}/feedback",
     *     tags={"Support"},
     *     summary="Submit article feedback",
     *     description="Mark an FAQ article as helpful or not helpful",
     *     @OA\Parameter(
     *         name="articleId",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"helpful"},
     *             @OA\Property(property="helpful", type="boolean")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Feedback recorded"
     *     )
     * )
     */
    public function articleFeedback(Request $request, int $articleId): JsonResponse
    {
        try {
            $article = FaqArticle::find($articleId);

            if (!$article) {
                return response()->json([
                    'status' => 'error',
                    'error' => 'Article not found',
                ], 404);
            }

            $validated = $request->validate([
                'helpful' => 'required|boolean',
            ]);

            if ($validated['helpful']) {
                $article->markHelpful();
            } else {
                $article->markNotHelpful();
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Thank you for your feedback!',
                'article' => [
                    'id' => $article->id,
                    'helpful_count' => $article->helpful_count,
                    'not_helpful_count' => $article->not_helpful_count,
                    'helpfulness' => $article->helpfulness_ratio,
                ],
            ], 200);
        } catch (Exception $e) {
            Log::error('Failed to record feedback: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'error' => 'Failed to record feedback',
            ], 500);
        }
    }

    /**
     * Get support contact information and SLA
     */
    public function contactInfo(): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'contact' => [
                'email' => 'support@groupsave.app',
                'critical_email' => 'urgent@groupsave.app',
            ],
            'sla' => [
                'starter' => [
                    'plan' => 'Starter (Free)',
                    'response_time' => '24–48 hours',
                ],
                'growth' => [
                    'plan' => 'Growth Plan',
                    'response_time' => '12–24 hours',
                ],
                'critical' => [
                    'plan' => 'Critical Issues',
                    'response_time' => '6–12 hours',
                ],
            ],
            'channels' => [
                [
                    'type' => 'email',
                    'label' => 'Email Support',
                    'value' => 'support@groupsave.app',
                    'note' => '24–48hr response',
                    'available' => true,
                ],
                [
                    'type' => 'chat',
                    'label' => 'Live Chat',
                    'value' => null,
                    'note' => 'Coming soon',
                    'available' => false,
                ],
                [
                    'type' => 'urgent',
                    'label' => 'Critical Escalation',
                    'value' => 'urgent@groupsave.app',
                    'note' => '6–12hr response',
                    'available' => true,
                ],
            ],
        ], 200);
    }
}
