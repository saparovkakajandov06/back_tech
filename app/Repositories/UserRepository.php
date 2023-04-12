<?php


namespace App\Repositories;


use App\User;

class UserRepository
{

    /**
     * @param int $parentId
     * @return User|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder
     */
    public function getReferalsByParent(int $parentId)
    {
        return User::where('parent_id', $parentId)
            ->select([
                'id', 'name', 'created_at', 'updated_at', 'email', 'roles', 'avatar',
                'ref_code', 'parent_id', 'cur'
            ]);
    }

    public function getRefs(int $parentId, $offset=0, $limit=10)
    {
        $q = User::where('parent_id', $parentId)
            ->select([
                'id', 'name', 'created_at', 'updated_at', 'email', 'roles', 'avatar',
                'ref_code', 'parent_id', 'cur'
            ]);
        $count = $q->count();
        $items = $q->offset($offset)
                   ->limit($limit)
                   ->get();

        return [$items, $count];
    }
}
