<?php

namespace App\Http\Controllers;

use App\Http\Requests\Posts\PostsCreateRequest;
use App\Http\Requests\Posts\PostsIndexRequest;
use App\Services\PostService;
use App\Traits\ApiResponser;
use App\Traits\AuditTrail;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PostsController extends Controller
{
    use ApiResponser;

    use AuditTrail;

    /**
     * @var App\Service\PostService $postService
     */
    private PostService $postService;

    public function __construct(PostService $postService)
    {
        $this->postService = $postService;
    }

    /**
     * @param App\Http\Requests\Posts\PostsIndexRequest $request
     * 
     * @return Illuminate\Http\Response
     */
    public function index(PostsIndexRequest $request): Response
    {
        $data = $request->validated();
        
        $posts = $this->postService
            ->get([
                'user' => config('user'),
                'keyword' => empty($data['keyword'])
                    ? null
                    : $data['keyword'],
                'dateFrom' => empty($data['dateFrom'])
                    ? null
                    : $data['dateFrom'],
                'dateTo' => empty($data['dateTo'])
                    ? null
                    : $data['dateTo'],
                'departmentId' => empty($data['departmentId'])
                    ? null
                    : $data['departmentId'],
                'projectId' => empty($data['projectId'])
                    ? null
                    : $data['projectId'],
                'sortBy' => $data['sortBy'] ?? 'created_at',
                'sorting' => $data['sorting'] ?? 'desc',
                'limit' => $data['limit'] ?? 50
            ]);

        return $this->apiResponse(
            'Posts were retrieved successfully',
            $posts,
            200
        );
    }

    /**
     * @var string $id
     * 
     * @return Illuminate\Http\Response
     */
    public function show(string $id): Response
    {
        $project = $this->postService->findById($id);

        return $this->apiResponse(
            'Post was retrieved successfully',
            $project,
            200
        );
    }

    /**
     * @param App\Http\Requests\Posts\PostsCreateRequest $request
     * 
     * @return Illuminate\Http\Response
     */
    public function store(PostsCreateRequest $request): Response
    {
        $user = config('user');
        $data = $request->validated();

        $post = $this->postService->create([
            'post' => empty($data['post'])
                ? null
                : $data['post'],
            'departmentId' => empty($data['departmentId'])
                ? null
                : $data['departmentId'],
            'projectId' => empty($data['projectId'])
                ? null
                : $data['projectId'],
            'created_by' => $user['id'],
        ]);

        $this->saveLogs(
            'Posts',
            $data,
            'Added a post - ' . $post->id
        );

        return $this->apiResponse(
            'Post was stored successfully',
            $post,
            201
        );
    }

    /**
     * @param App\Http\Requests\Posts\PostsCreateRequest $request
     * 
     * @return Illuminate\Http\Response
     */
    public function update(PostsCreateRequest $request, $id): Response
    {
        $data = $request->validated();
        $user = config('user');

        $data = [
            'post' => empty($data['post'])
                ? null
                : $data['post'],
            'departmentId' => empty($data['departmentId'])
                ? null
                : $data['departmentId'],
            'projectId' => empty($data['projectId'])
                ? null
                : $data['projectId'],
            'created_by' => $user['id'],
        ];

        $post = $this->postService
            ->update(
                $id,
                $data
            );

        $this->saveLogs(
            'Posts',
            $data,
            'Updated a post - ' . $id
        );

        return $this->apiResponse(
            'Post was updated successfully',
            $post,
            200
        );
    }

    /**
     * @param string $id
     * 
     * @return Illuminate\Http\Response
     */
    public function destroy(string $id): Response
    {
        $post = $this->postService->delete($id);

        if (!$post) {
            return $this->apiResponse(
                'Post was not found',
                null,
                404
            );
        }

        $this->saveLogs(
            'Posts',
            $id,
            'Deleted a post - ' . $id
        );

        return $this->apiResponse(
            'Post was deleted successfully',
            null,
            200
        );
    }
}
