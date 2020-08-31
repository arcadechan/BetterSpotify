<template>
    <div>
        <p v-if="generation == 'pending'">The first step is to get all the artists you follow. Click the "Get Artists" button when you're ready!</p>
        <p v-if="generation == 'gettingArtists'">Getting followed artists...</p>
        <p v-if="generation == 'artistsRetrieved'">Artists retrieved! Double check your list and if the list of artists looks ok, press the button to generate and create the Better Release Radar straight into your account.</p>
        <p v-if="generation == 'generatingPlaylist'">Generating Playlist...</p>

        <button v-on:click="generatePlaylist" class="btn btn-spotify m-1" v-if="generation == 'pending' || generation == 'artistsRetrieved'">
            <template v-if="generation == 'pending'">Get Artists</template>
            <template v-if="generation == 'artistsRetrieved'">Generate Better Release Radar</template>
        </button>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                generation: 'pending',
                artists: {}
            }
        },
        methods: {
            generatePlaylist: function() {
                const self = this;

                this.generation = 'gettingArtists';
                
                axios.get('/api/spotify/get_artists')
                .then( response => {
                    self.generation = 'artistsRetrieved';
                    self.artists = response.data;    
                }).catch( error => {
                    
                    console.log(error);
                });
            }
        }
    }
</script>