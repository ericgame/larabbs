<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SeedCategoriesData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $categories = [
            [
                'name'        => '分享',
                'description' => '分享創造，分享發現',
            ],
            [
                'name'        => '課程',
                'description' => '開發技巧、推薦擴展包等',
            ],
            [
                'name'        => '問答',
                'description' => '請保持友善，互相幫助',
            ],
            [
                'name'        => '公告',
                'description' => '網站公告',
            ],
        ];

        DB::table('categories')->insert($categories);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('categories')->truncate();
    }
}
