<?php

namespace Tests\Feature;

use App\Models\Artist;
use App\Models\Song;
use App\Models\SongStart;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SongStartTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_authenticated_users_can_start_a_song(): void
    {
        $song = $this->createSong();

        $this->post(route('song-starts.store', $song))
            ->assertRedirect(route('login'));

        $this->assertDatabaseCount('song_starts', 0);
    }

    public function test_starting_a_song_creates_an_announcement_available_to_listeners(): void
    {
        $song = $this->createSong();
        $user = User::factory()->create(['username' => 'song-leader']);

        $this->actingAs($user)
            ->from(route('song.index'))
            ->post(route('song-starts.store', $song))
            ->assertRedirect(route('song.index'));

        $start = SongStart::firstOrFail();
        $this->assertSame($user->id, $start->started_by);
        $this->assertSame($song->name, $start->song_name);

        $this->getJson(route('song-starts.poll', ['after' => 0]))
            ->assertOk()
            ->assertJsonPath('starts.0.song_name', $song->name)
            ->assertJsonPath('starts.0.artist_name', 'Test Artist')
            ->assertJsonPath('starts.0.url', route('song.show', $song));

        $this->getJson(route('song-starts.poll', ['after' => $start->id]))
            ->assertOk()
            ->assertExactJson(['starts' => []]);
    }

    private function createSong(): Song
    {
        $artist = new Artist();
        $artist->name = 'Test Artist';
        $artist->save();

        $song = new Song();
        $song->name = 'Test Song';
        $song->text = 'Test lyrics';
        $song->artist()->associate($artist);
        $song->save();

        return $song;
    }
}
