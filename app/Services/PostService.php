<?php

namespace App\Services;

use App\Repositories\PostRepository;
use Illuminate\Pagination\LengthAwarePaginator;

class PostService
{
    /**
     * @var App\Repositories\PostRepository $postRepository
     */
    private PostRepository $postRepository;

    public function __construct(PostRepository $postRepository)
    {
        $this->postRepository = $postRepository;
    }

    /**
     * @param array $params
     * 
     * @return Illuminate\Pagination\LengthAwarePaginator
     */
    public function get(array $params): LengthAwarePaginator
    {
        return $this->postRepository->get($params);
    }

    /**
     * @param string $id
     * 
     * @return object 
     */
    public function findById(string $id): object
    {
        return $this->postRepository->findById($id);
    }

    /**
     * @param array $data
     * 
     * @return object
     */
    public function create(array $data): object
    {
        return $this->postRepository->create($data);
    }

    /**
     * @param string $id
     * @param array $data
     * 
     * @return object
     */
    public function update(
        string $id,
        array $data
    ): object {
        return $this->postRepository
            ->update(
                $id,
                $data
            );
    }

    /**
     * @param string $id
     * 
     * @return bool
     */
    public function delete(string $id): bool
    {
        return $this->postRepository->delete($id);
    }
}
