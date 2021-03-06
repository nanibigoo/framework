<?php
/**
 * This file is part of Notadd.
 *
 * @author        TwilRoad <heshudong@ibenchu.com>
 * @copyright (c) 2017, notadd.com
 * @datetime      2017-10-24 15:24
 */
namespace Notadd\Foundation\Setting\GraphQL\Mutations;

use GraphQL\Type\Definition\Type;
use Notadd\Foundation\GraphQL\Abstracts\Mutation;

/**
 * Class SettingMutation.
 */
class SettingsMutation extends Mutation
{
    /**
     * @return array
     */
    public function args(): array
    {
        return [
            'key'   => [
                'name' => 'key',
                'type' => Type::nonNull(Type::string()),
            ],
            'value' => [
                'name' => 'value',
                'type' => Type::nonNull(Type::string()),
            ],
        ];
    }

    /**
     * @param $root
     * @param $args
     *
     * @return mixed|void
     */
    public function resolve($root, $args)
    {
        $this->setting->set($args['key'], $args['value']);
    }

    /**
     * @return \GraphQL\Type\Definition\ListOfType
     */
    public function type()
    {
        return $this->graphql->type('settings');
    }
}
