<?php

namespace Database\Seeders;

use App\Models\Album;
use App\Models\Artist;
use App\Models\Song;
use Illuminate\Database\Seeder;

class SpotifyDBSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // load spotify database
        $file = database_path('/csv/spotify_tracks.csv');
        $csv_reader = new CSVReader($file);

        // current time
        $current_time = now();

        // loop the data as it's been yielded
        foreach ($csv_reader->csvToArray() as $data) {
            // data place holder
            $albums = array();
            $artists = array();
            $songs = array();

            // preprocessing the data
            foreach ($data as $key => $entry) {

                // get array of artists & ids for each song
                $artist_ids = str_to_array($data[$key]['artist_ids']);
                $artists_name = str_to_array($data[$key]['artists']);
                // loop over them and add it to the array
                for($i = 0; $i < sizeof($artists_name); $i++) {
                    // build artists array
                    $artists[] = array(
                        'id' => $artist_ids[$i],
                        'name' => $artists_name[$i],
                        'created_at' => $current_time,
                        'updated_at' => $current_time,
                    );

                    // build albums array
                    $albums[] = array(
                        'id' => $data[$key]['album_id'],
                        'artist_id' => $artist_ids[$i],
                        'name' => $data[$key]['album'],
                        'created_at' => $current_time->timestamp,
                        'updated_at' => $current_time->timestamp,
                    );

                    // build songs array
                    $songs[] = array(
                        'id' => $data[$key]['id'],
                        'album_id' => $data[$key]['album_id'],
                        'artist_id' => $artist_ids[$i],
                        'title' => $data[$key]['name'],
                        'track_number' => $data[$key]['track_number'],
                        'disc_number' => $data[$key]['disc_number'],
                        'duration_ms' => $data[$key]['duration_ms'],
                        'year' => $data[$key]['year'],
                        'release_date' => $data[$key]['release_date'],
                        'created_at' => $current_time,
                        'updated_at' => $current_time,
                    );
                }
            }

            // inserting the data
            // artist
            $artists = array_unique($artists, SORT_REGULAR);
            Artist::insertOrIgnore($artists);

            // albums
            $albums = array_unique($albums, SORT_REGULAR);
            Album::insertOrIgnore($albums);

            // songs
            $songs = array_unique($songs, SORT_REGULAR);
            Song::insertOrIgnore($songs);
        }
    }
}
