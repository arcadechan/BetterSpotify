<template>
    <div>
        <div class="col-12 justify-content-center">
            <button 
                v-if="generation == 'pending' || generation == 'artistsRetrieved' ||  generation == 'albumsRetrieved'"
                @click="getArtists"
                class="btn btn-spotify mx-auto my-4 d-block">
                Get Artists
            </button>

            <button
                v-if="generation == 'artistsRetrieved' || generation == 'albumsRetrieved'"
                @click="generatePlaylist"
                class="btn btn-spotify mx-auto my-4 d-block">
                Generate Better Release Radar
            </button>
        </div>

        <p v-if="generation == 'pending'">The first step is to get all the artists you follow. Click the "Get Artists" button when you're ready!</p>
        <div class="d-block text-center">
            <p v-if="generation == 'gettingArtists'">Getting followed artists...</p>
            <p v-if="generation == 'generatingPlaylist'">Generating Playlist...</p>
        </div>

        <div v-if="generation == 'gettingArtists' || generation == 'generatingPlaylist'">
            <div class="text-center">
                <div class="spinner-border text-spotify m-5" role="status" style="width: 3rem; height: 3rem;">
                    <span class="sr-only">Loading...</span>
                </div>
            </div>
        </div>

        <div v-if="generation == 'artistsRetrieved' || generation == 'albumsRetrieved'">
            <p v-if="artistsInStorage">Here is a list of your followed artists we saved from the last time you fetched them. If the artists you follow hasn't changed, you can go ahead and just hit the "Generate Better Release Radar" button. Otherwise you can hit the "Get Artists" button to get your followed artists again.</p>
            <p v-else>Artists retrieved! Double check your list and if the list of artists looks ok, press the "Generate Better Release Radar" to create the playlist into your account.</p>
            <p>You can click on each artist card to navigate to their artist page on Spotify.</p>
            
            <button @click="artistGalleryOpen = !artistGalleryOpen" class="d-block btn btn-dark mx-auto" type="button" data-toggle="collapse" data-target="#artist-gallery" aria-expanded="false" aria-controls="artist-gallery">
                {{ artistGalleryOpen ? 'Hide' : 'Show' }} Artist List
            </button>

            <h6 class="d-block text-center my-4">Artist Count: {{ artists.length }}</h6>

            <div id="artist-gallery" class="col-12 p-0 mx-auto my-4 show">
                <div v-for="(artist, index) in artists" :key="artist.id" class="artist-container col-12 col-md-6 col-xl-4 p-0">
                    <a :href="artist.external_urls.spotify" target="_blank">
                        <div class="artist-no">{{ index + 1}}</div>
                        <div v-if="artist.images.length > 0" class="artist-image-container">
                            <img class="artist-image" :src="artist.images[2]['url']" alt="">
                        </div>
                        <div v-else class="artist-image-container">
                            <svg role="img" viewBox="-25 -22 100 100" class="artist-image no-artist-image" ><path d="M35.711 34.619l-4.283-2.461a1.654 1.654 0 0 1-.808-1.156 1.65 1.65 0 0 1 .373-1.36l3.486-4.088a14.3 14.3 0 0 0 3.432-9.293V14.93c0-3.938-1.648-7.74-4.522-10.435C30.475 1.764 26.658.398 22.661.661c-7.486.484-13.35 6.952-13.35 14.725v.875c0 3.408 1.219 6.708 3.431 9.292l3.487 4.089a1.656 1.656 0 0 1-.436 2.516l-8.548 4.914A14.337 14.337 0 0 0 0 49.513V53.5h2v-3.987c0-4.417 2.388-8.518 6.237-10.705l8.552-4.916a3.648 3.648 0 0 0 1.783-2.549 3.643 3.643 0 0 0-.822-2.999l-3.488-4.091a12.297 12.297 0 0 1-2.951-7.993v-.875c0-6.721 5.042-12.312 11.479-12.729 3.449-.22 6.725.949 9.231 3.298a12.182 12.182 0 0 1 3.89 8.976v1.331c0 2.931-1.048 5.77-2.952 7.994l-3.487 4.089a3.653 3.653 0 0 0-.822 3 3.653 3.653 0 0 0 1.782 2.548l3.036 1.745a11.959 11.959 0 0 1 2.243-1.018zM45 25.629v15.289a7.476 7.476 0 0 0-5.501-2.418c-4.135 0-7.5 3.365-7.5 7.5s3.364 7.5 7.5 7.5 7.5-3.365 7.5-7.5V29.093l5.861 3.384 1-1.732L45 25.629zM39.499 51.5a5.506 5.506 0 0 1-5.5-5.5c0-3.033 2.467-5.5 5.5-5.5s5.5 2.467 5.5 5.5-2.467 5.5-5.5 5.5z" fill="currentColor" fill-rule="evenodd"></path></svg>
                        </div>
                        <div class="artist-name">{{ artist.name }}</div>
                    </a>
                </div>
            </div>
        </div>

        <div v-if="generation == 'albumsRetrieved'" class="my-5">
            <p v-if="albumsInStorage">Here is the last "Better Release Radar" you generated.</p>
            <p v-else>Your playlist has been generated! Here are the the latest releases we found and added to your list! To generate again click "Generate Better Release Radar".</p>

            <button @click="albumGalleryOpen = !albumGalleryOpen" class="d-block btn btn-dark mx-auto" type="button" data-toggle="collapse" data-target="#album-gallery" aria-expanded="false" aria-controls="album-gallery">
                {{ albumGalleryOpen ? 'Hide' : 'Show' }} Album List
            </button>

            <h6 class="d-block text-center my-4">Album Count: {{ albums.length }}</span></h6>
            <h6 v-if="trackCount" class="d-block text-center my-4">Track Count: {{ trackCount }}</h6>

            <div id="album-gallery" class="col-12 p-0 my-4 show">
                <div v-for="(album, index) in albums" :key="album.id" class="album-container col-12 col-md-6 col-lg-4 col-xl-3 p-0">
                    <div>
                        <div class="album-inner-container text-center">
                            <template v-if="album.images.length > 0">
                                <template v-if="!!album.images[1]">
                                    <img :src="album.images[1]['url']" class="album-image" alt="">
                                </template>
                                <template v-else>
                                    <img :src="album.images[0]['url']" class="album-image" alt="">
                                </template>
                            </template>
                            <div class="flip-card mt-2">
                                <div :id="`card-${index}`" class="table-container flip-card-inner">
                                    <div class="flip-card-front">
                                        <table class="mt-2">
                                            <tbody>
                                                <tr>
                                                    <template v-if="album.artists.length == 1">
                                                        <td>Artist:</td>
                                                        <td><a :href="album.artists[0].external_urls.spotify" target="_blank" class="album-artist-link">{{ album.artists[0].name }}</a></td>
                                                    </template>
                                                    <template v-else>
                                                        <td>Artists:</td>
                                                        <td>
                                                            <template v-for="(artist, index) in album.artists">
                                                                <a :href="artist.external_urls.spotify" :key="index" target="_blank" class="album-artist-link">{{ artist.name }}</a><template v-if="index < album.artists.length - 1">, </template>
                                                            </template>
                                                        </td>
                                                    </template>
                                                </tr>
                                                <tr>
                                                    <td>Album:</td>
                                                    <td><a :href="album.external_urls.spotify" class="album-link">{{ album.name }}</a></td>
                                                </tr>
                                                <tr>
                                                    <td>Type:</td>
                                                    <td style="text-transform: capitalize;">{{ album.album_type }}</td>
                                                </tr>
                                                <tr>
                                                    <td>Released:</td>
                                                    <td>{{ album.release_date }}</td>
                                                </tr>
                                                <tr>
                                                    <td>Tracks:</td>
                                                    <td>{{ album.total_tracks }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="flip-card-back">
                                        <div class="track-list-container">
                                            <table>
                                                <thead>
                                                    <tr>
                                                        <td class="track-no">Track #</td>
                                                        <td class="track-title">Title</td>
                                                        <td></td>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr v-for="track in tracks[album.id]" :key="track.id" :class="{ 'playing' : track.name == previewTrack }">
                                                        <td class="track-no-row">{{ track.track_number }}</td>
                                                        <td>{{ track.name }}</td>
                                                        <td @click="previewUrl = track.preview_url; previewArtists = track.artists; previewTrack = track.name" class="track-preview">
                                                            <i class="fas fa-play-circle" :class="{ 'playing-icon' : track.name == previewTrack }"></i>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <button class="btn btn-spotify btn-sm mt-3 flip-card-button" @click="flipCard(index)">
                                <i class="fas fa-redo-alt"></i>View album <span :id="`flip-view-tracks-${index}`">tracks</span><span :id="`flip-view-albums-${index}`">info</span>.
                            </button>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <div v-show="!!previewUrl" id="audio" class="player-wrapper">
            <audio-player :file="previewUrl" :artists="previewArtists" :track="previewTrack" @closed="closePlayer"></audio-player>
        </div>

    </div>
</template>

<script>
    export default {
        data() {
            return {
                generation: 'pending',
                artists: [],
                albums: [],
                tracks: [],
                artistsInStorage: false,
                albumsInStorage: false,
                tracksInStorage: false,
                artistGalleryOpen: true,
                albumGalleryOpen: true,
                previewUrl: null,
                previewArtists: [],
                previewTrack: null
            }
        },
        methods: {
            getArtists: function() {
                const self = this;

                this.generation = 'gettingArtists';
                this.artistsInStorage = false;

                axios.get('/api/spotify/get_artists')
                .then( response => {
                    self.generation = 'artistsRetrieved';
                    self.artists = response.data;
                                        
                    localStorage.setItem('artists', JSON.stringify(self.artists));
                    
                }).catch( error => {
                    self.generation = 'pending';
                    console.log(error);
                });
            },
            generatePlaylist: function() {
                const self = this;

                this.generation = 'generatingPlaylist';

                const artists = this.artists;

                axios.post('/api/spotify/create_playlist', { artists })
                .then( response => {
                    self.generation = 'albumsRetrieved';
                    self.albums = response.data.albums;
                    self.tracks = response.data.tracks;

                    localStorage.setItem('albums', JSON.stringify(self.albums));
                    localStorage.setItem('tracks', JSON.stringify(self.tracks));
                }).catch( error => {
                    console.log(error);
                });

            },
            flipCard: function(index){
                let card = document.querySelector(`#card-${index}`);
                card.classList.toggle('flipped');

                let trackText = document.querySelector(`#flip-view-tracks-${index}`);
                let albumText = document.querySelector(`#flip-view-albums-${index}`);

                if(card.classList.contains('flipped')) {
                    trackText.style.display = 'none';
                    albumText.style.display = 'inline-block';
                } else {
                    trackText.style.display = 'inline-block';
                    albumText.style.display = 'none';
                }
            },
            closePlayer: function() {
                this.previewUrl = null;
                this.previewArtists = [];
                this.previewTrack = null;
            }
        },
        computed: {
            trackCount: function(){
                let count = 0;

                if(!!this.tracks){
                    for(const track in this.tracks){
                        count += this.tracks[track].length;
                    }
                }

                return count;
            }
        },
        mounted() {
            const oldArtistList = localStorage.getItem('artists');

            if(!!oldArtistList){
                this.artistsInStorage = true;
                this.artists = JSON.parse(oldArtistList);;
                this.generation = 'artistsRetrieved';
            }

            const oldAlbumsList = localStorage.getItem('albums');
            const oldTracksList = localStorage.getItem('tracks');

            if(!!oldAlbumsList && !!oldTracksList){
                this.albumsInStorage = true;
                this.albums = JSON.parse(oldAlbumsList);

                this.tracksInStorage = true;
                this.tracks = JSON.parse(oldTracksList);

                this.generation = 'albumsRetrieved';
            }
        }
    }
</script>