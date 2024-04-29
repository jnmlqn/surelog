<?php

namespace App\Repositories;

use App\Models\Post;
use Illuminate\Pagination\LengthAwarePaginator;

class PostRepository
{
    /**
     * @var App\Models\Post $post
     */
	private Post $post;

	public function __construct(Post $post)
	{
		$this->post = $post;
	}

    /**
     * @param array $params
     * 
     * @return Illuminate\Pagination\LengthAwarePaginator
     */
    public function get(array $params): LengthAwarePaginator
    {
        return $this->post
            ->select(
                'posts.*',
                'p.name as project_name',
                'd.name as department_name',
            )
            ->when(!is_null($params['keyword']), function ($q) use ($params) {
                $q->where('posts.post', 'LIKE', "%{$params['keyword']}%")
                    ->orWhere('p.name', 'LIKE', "%{$params['keyword']}%")
                    ->orWhere('d.name', 'LIKE', "%{$params['keyword']}%");
            })
            ->when(!is_null($params['departmentId']), function ($q) use ($params) {
                $q->where('posts.department_id', $params['departmentId']);
            })
            ->when(!is_null($params['projectId']), function ($q) use ($params) {
                $q->where('posts.project_id', $params['projectId']);
            })
            ->when(!is_null($params['dateFrom']) && !is_null($params['dateTo']), function ($q) use ($params) {
                $q->whereBetween('posts.created_at', [$params['dateFrom'], $params['dateTo']]);
            })
            ->leftJoin('projects as p', 'p.id', '=', 'posts.project_id')
            ->leftJoin('departments as d', 'd.id', '=', 'posts.department_id')
            ->with('createdBy:id,first_name,middle_name,last_name,extension')
            ->orderBy($params['sortBy'], $params['sorting'])
            ->paginate($params['limit']);
    }

    /**
     * @param string $id
     * 
     * @return object 
     */
    public function findById(string $id): object
    {
        return $this->post
            ->with('projectId')
            ->findOrFail($id);
    }

    /**
     * @param array $data
     * 
     * @return object
     */
    public function create(array $data): object
    {
        return $this->post->create($data);
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
        $post = $this->post->findOrFail($id);
        $post->fill($data)->save();
        return $post;
    }

    /**
     * @param string $id
     * 
     * @return bool
     */
    public function delete(string $id): bool
    {
        return $this->post->where('id', $id)->delete();
    }
}
