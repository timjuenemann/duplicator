<?php

namespace DoubleThreeDigital\Duplicator\Tests\Actions;

use DoubleThreeDigital\Duplicator\Actions\DuplicateEntryAction;
use DoubleThreeDigital\Duplicator\Tests\TestCase;
use Illuminate\Support\Facades\Config;
use Statamic\Facades\Entry;
use Statamic\Facades\Site;
use Statamic\Sites\Sites;
use Statamic\Structures\CollectionStructure;

class DuplicateEntryActionTest extends TestCase
{
    public $user;
    public $action;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = $this->makeStandardUser();
        $this->action = new DuplicateEntryAction();
    }

    /** @test */
    public function cant_get_field_items_for_single_site()
    {
        $this->markTestIncomplete();
    }

    /** @test */
    public function can_get_field_items_for_multi_site()
    {
        $this->markTestIncomplete();
    }

    /** @test */
    public function is_visible_for_entries()
    {
        $collection = $this->makeCollection('articles', 'Articles');
        $entry = $this->makeEntry('articles', 'fresh-article', $this->user);

        $visible = $this->action->visibleTo($entry);

        $this->assertTrue($visible);
    }

    /** @test */
    public function is_not_visible_for_non_entries()
    {
        $collection = $this->makeCollection('gallery', 'Photo Gallery');

        $visible = $this->action->visibleTo($collection);

        $this->assertFalse($visible);
    }

    /** @test */
    public function can_duplicate_entry()
    {
        $collection = $this->makeCollection('guides', 'Guides');
        $entry = $this->makeEntry('guides', 'fresh-guide', $this->user);

        $duplicate = $this->action->run(collect([$entry]), []);

        $duplicateEntry = Entry::findBySlug('fresh-guide-1', 'guides');

        $this->assertIsObject($duplicateEntry);
        $this->assertSame($duplicateEntry->slug(), 'fresh-guide-1');
    }

    /** @test */
    public function can_duplicate_entry_with_original_parent()
    {
        $collection = $this->makeCollection('recipies', 'Recipies');

        $entryParent = $this->makeEntry('recipies', 'cheese-toastie', $this->user);
        $entry = $this->makeEntry('recipies', 'sausage-roll', $this->user);

        $tree = [
            [
                'entry' => $entryParent->id(),
                'children' => [
                    [
                        'entry' => $entry->id(),
                    ],
                ],
            ],
        ];

        (new CollectionStructure)->collection($collection)
            ->in('default')
            ->tree($tree)
            ->save();

        $duplicate = $this->action->run(collect([$entry]), []);

        $duplicateEntry = Entry::findBySlug('sausage-roll-1', 'recipies');

        // $this->assertIsObject($duplicateEntry);
        // $this->assertSame($duplicateEntry->slug(), 'sausage-roll-duplicate');

        // dd($duplicateEntry->id(), $collection->structure()->in('default')->tree());

        // dump($collection->structure()->in('default')->tree(), $duplicateEntry->id());

        // // assert is in correct place in array
        // $this->assertSame($collection->structure()->in('default')->tree()[0]['entry'], $entryParent->id());
        // $this->assertSame($collection->structure()->in('default')->tree()[0]['children'][0]['entry'], $entry->id());
        // $this->assertSame($collection->structure()->in('default')->tree()[0]['children'][1]['entry'], $duplicateEntry->id());
    }

    /** @test */
    public function can_duplicate_entry_with_config_publish_state()
    {
        $this->markTestIncomplete();

        Config::set('duplicator.defaults.published', false);

        $collection = $this->makeCollection('blog', 'Blog');
        $entry = $this->makeEntry('blog', 'hello-world', $this->user);

        $duplicate = $this->action->run(collect([$entry]), []);

        $duplicateEntry = Entry::findBySlug('hello-world-1', 'blog');

        $this->assertIsObject($duplicateEntry);
        $this->assertSame($duplicateEntry->slug(), 'hello-world-1');
        $this->assertSame($duplicateEntry->published(), false);
    }

    /** @test */
    public function can_duplicate_entry_with_duplicated_entry_publish_state()
    {
        $this->markTestIncomplete();

        Config::set('duplicator.defaults.published', null);

        $collection = $this->makeCollection('blog', 'Blog');
        $entry = $this->makeEntry('blog', 'hello-universe', $this->user);

        $entry->published(false)->save();

        $duplicate = $this->action->run(collect([$entry]), []);

        $duplicateEntry = Entry::findBySlug('hello-universe-1', 'blog');

        $this->assertIsObject($duplicateEntry);
        $this->assertSame($duplicateEntry->slug(), 'hello-universe-1');
        $this->assertSame($duplicateEntry->published(), false);
    }

    /** @test */
    public function can_duplicate_entry_for_different_site()
    {
        $this->markTestIncomplete();
    }
}
