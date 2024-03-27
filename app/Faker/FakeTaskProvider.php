<?php
namespace App\Faker;
use Faker\Provider\Base;

class FakeTaskProvider extends Base
{
    protected static $tasks = [
        'Замена розеток',
        'Поклейка обоев',
        'Установка окон',
        'Замена проводки',
        'Замена батареи',
        'Сантехнические работы',
        'Покраска стен',
        'Шпаклёвка',
        'Демонтаж стен',
        'Установка плинтусов'
    ];
    public function task(): string
    {
        return static::randomElement(static::$tasks);
    }
}