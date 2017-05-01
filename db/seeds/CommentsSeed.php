<?php

use Phinx\Seed\AbstractSeed;

class CommentsSeed extends AbstractSeed
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeders is available here:
     * http://docs.phinx.org/en/latest/seeding.html
     */
    public function run()
    {
        $faker = \Faker\Factory::create();
        $data = [];
        for ($i = 2; $i < 40; $i++) {
            $comment = $this->generateComment($faker);

            $data[] = $comment;

            for ($j = 0; $j < rand(0,4); $j++) {
                $data[] = $this->generateComment($faker,$comment['id']);
            }
        }

        $this->insert('comments', $data);
    }

    /**
     * @param \Faker\Generator $faker
     * @param null $parent_id
     * @return array
     */

    private $id = 1;
    private function generateComment($faker, $parent_id = null)
    {
        $author = [
            'id'        => $this->id++,
            'author'    => $faker->firstName,
            'comment'   => $faker->text,
            'created_at'=> sprintf(
                '2017-%02d-%02d %02d:%02d:%02d',
                rand(1,3),
                rand(1,28),
                rand(0,23),
                rand(0,59),
                rand(0,59)
            ),
        ];
        if ($parent_id) {
            $author['parent_id'] = $parent_id;
        }
        echo "{$author['id']} | {$author['created_at']}\n";
        return $author;
    }
}
