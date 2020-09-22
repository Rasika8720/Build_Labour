<?php

namespace App\Repositories;

use App\Models\Skills\Skill;
use Torann\LaravelRepository\Repositories\AbstractRepository;

class SkillRepository extends AbstractRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    protected $model = Skill::class;

    /**
     * Valid searchable columns
     *
     * @return array
     */
    protected $searchable = [
        'query' => [
            'name',
        ],
    ];

}