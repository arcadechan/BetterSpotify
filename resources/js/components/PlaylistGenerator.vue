<template>
    <div>
        <p v-if="generation == 'pending'">The first step is to get all the artists you follow. Click the "Get Artists" button when you're ready!</p>
        <p v-if="generation == 'gettingArtists'">Getting followed artists...</p>
        <div v-if="generation == 'artistsRetrieved'">
            <p v-if="artistsInStorage">Here is a list of your followed artists we saved from your last visit. If the artists you follow hasn't changed, you can go ahead and just hit the "Generate Better Release Radar" button. Otherwise you can hit the "Get Artists" button to get your followed artists again.</p>
            <p v-else>Artists retrieved! Double check your list and if the list of artists looks ok, press the button to generate and create the Better Release Radar straight into your account.</p>
            <table id="artists-table" class="col-12 col-lg-10 mx-auto">
                <thead>
                    <tr>
                        <td></td>
                        <td></td>
                        <td>Artist Name</td>
                        <td>Link to Spotify Artist Page</td>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(artist, index) in artists" :key="artist.id">
                        <td>{{ index + 1}}</td>
                        <td v-if="artist.images.length > 0">
                            <img class="artist-image" :src="artist.images[2]['url']" alt="">
                        </td>
                        <td v-else>
                            <svg role="img" viewBox="-25 -22 100 100" class="artist-image no-artist-image" ><path d="M35.711 34.619l-4.283-2.461a1.654 1.654 0 0 1-.808-1.156 1.65 1.65 0 0 1 .373-1.36l3.486-4.088a14.3 14.3 0 0 0 3.432-9.293V14.93c0-3.938-1.648-7.74-4.522-10.435C30.475 1.764 26.658.398 22.661.661c-7.486.484-13.35 6.952-13.35 14.725v.875c0 3.408 1.219 6.708 3.431 9.292l3.487 4.089a1.656 1.656 0 0 1-.436 2.516l-8.548 4.914A14.337 14.337 0 0 0 0 49.513V53.5h2v-3.987c0-4.417 2.388-8.518 6.237-10.705l8.552-4.916a3.648 3.648 0 0 0 1.783-2.549 3.643 3.643 0 0 0-.822-2.999l-3.488-4.091a12.297 12.297 0 0 1-2.951-7.993v-.875c0-6.721 5.042-12.312 11.479-12.729 3.449-.22 6.725.949 9.231 3.298a12.182 12.182 0 0 1 3.89 8.976v1.331c0 2.931-1.048 5.77-2.952 7.994l-3.487 4.089a3.653 3.653 0 0 0-.822 3 3.653 3.653 0 0 0 1.782 2.548l3.036 1.745a11.959 11.959 0 0 1 2.243-1.018zM45 25.629v15.289a7.476 7.476 0 0 0-5.501-2.418c-4.135 0-7.5 3.365-7.5 7.5s3.364 7.5 7.5 7.5 7.5-3.365 7.5-7.5V29.093l5.861 3.384 1-1.732L45 25.629zM39.499 51.5a5.506 5.506 0 0 1-5.5-5.5c0-3.033 2.467-5.5 5.5-5.5s5.5 2.467 5.5 5.5-2.467 5.5-5.5 5.5z" fill="currentColor" fill-rule="evenodd"></path></svg>
                        </td>
                        <td>{{ artist.name }}</td>
                        <td><a :href="artist.external_urls.spotify" target="_blank">Link</a></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <p v-if="generation == 'generatingPlaylist'">Generating Playlist...</p>

        <div class="col-12 justify-content-center">
            <button v-on:click="getArtists" class="btn btn-spotify mx-auto my-4 d-block" v-if="generation == 'pending' || generation == 'artistsRetrieved'">
                <template>Get Artists</template>
            </button>

            <button v-on:click="generatePlaylist" class="btn btn-spotify mx-auto my-4 d-block" v-if="generation == 'artistsRetrieved'">
                <template v-if="generation == 'artistsRetrieved'">Generate Better Release Radar</template>
            </button>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                generation: 'pending',
                artists: {},
                artistsInStorage: false
            }
        },
        methods: {
            getArtists: function() {
                const self = this;

                this.generation = 'gettingArtists';

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

                }).catch( error => {
                    console.log(error);
                });

            }
        },
        mounted() {
            const oldArtistList = localStorage.getItem('artists');

            if(!!oldArtistList){
                this.artistsInStorage = true;
                this.artists = JSON.parse(oldArtistList);;
                this.generation = 'artistsRetrieved';
                
            }
        }
    }
</script>