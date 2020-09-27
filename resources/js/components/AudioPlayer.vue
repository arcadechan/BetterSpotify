<template>
    <div class="player">
        <div class="track-info">
            <p class="track-marquee">
                <!-- <template v-for="(artist, index) in artists">
                        {{ artist.name }}<template v-if="index < artists.length - 1">, </template>
                </template>
                - {{ track }} -->
                {{ trackInfo }}
            </p>
        </div>
        <div class="player-controls">
            <div class="player-button">
                <a v-on:click.prevent="stop" title="Stop" href="#" class="stop">
                    <svg width="18px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                        <path fill="currentColor" d="M16,4.995v9.808C16,15.464,15.464,16,14.804,16H4.997C4.446,16,4,15.554,4,15.003V5.196C4,4.536,4.536,4,5.196,4h9.808C15.554,4,16,4.446,16,4.995z"/>
                    </svg>
                </a>
            </div>
            <div class="player-button">
                <a v-on:click.prevent="playing = !playing" title="Play/Pause" href="#" :class="{ pressed : playing }">
                    <svg width="18px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                        <path v-if="!playing" fill="currentColor" d="M15,10.001c0,0.299-0.305,0.514-0.305,0.514l-8.561,5.303C5.51,16.227,5,15.924,5,15.149V4.852c0-0.777,0.51-1.078,1.135-0.67l8.561,5.305C14.695,9.487,15,9.702,15,10.001z"/>
                        <path v-else fill="currentColor" d="M15,3h-2c-0.553,0-1,0.048-1,0.6v12.8c0,0.552,0.447,0.6,1,0.6h2c0.553,0,1-0.048,1-0.6V3.6C16,3.048,15.553,3,15,3z M7,3H5C4.447,3,4,3.048,4,3.6v12.8C4,16.952,4.447,17,5,17h2c0.553,0,1-0.048,1-0.6V3.6C8,3.048,7.553,3,7,3z"/>
                    </svg>
                </a>
            </div>
            <div>
                <div v-on:click="seek" class="player-progress" title="Time played : Total time">
                    <div :style="{ width: this.percentComplete + '%' }" class="player-seeker"></div>
                </div>
                <div class="player-time">
                    <div class="player-time-current">{{ currentTime }}</div>
                    <div class="player-time-total">{{ durationTime }}</div>
                </div>
            </div>
            <div>
                <div v-on:click.prevent="" title="Volume" class="volume d-none d-md-flex">
                    <svg width="18px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                        <path fill="currentColor" d="M19,13.805C19,14.462,18.462,15,17.805,15H1.533c-0.88,0-0.982-0.371-0.229-0.822l16.323-9.055C18.382,4.67,19,5.019,19,5.9V13.805z"/>
                    </svg>
                    <input v-model.lazy.number="volume" v-show="showVolume" type="range" min="0" max="100"/>
                </div>
            </div>
            <div v-on:click.prevent="closePlayer" class="closePlayer player-button">
                <div title="Mute">
                    <i class="fas fa-times"></i>
                </div>
            </div>
        </div>
        <audio ref="audiofile" :src="file" preload="auto" style="display: none;"></audio>
    </div>
</template>
<script>
    export default {
        data() {
            return {
                audio: undefined,
                currentSeconds: 0,
                durationSeconds: 0,
                loaded: false,
                playing: false,
                previousVolume: 35,
                showVolume: true,
                volume: 30
            }
        },
        props: {
            file: {
                type: String,
                default: null
            },
            artists: {
                type: Array,
                default: []
            },
            track: {
                type: String,
                default: null
            },
            autoPlay: {
                type: Boolean,
                default: true
            },
            loop: {
                type: Boolean,
                default: false
            }
        },
        computed: {
            currentTime() {
                return convertTimeHHMMSS(this.currentSeconds);
            },
            durationTime() {
                return convertTimeHHMMSS(this.durationSeconds);
            },
            percentComplete() {
                return parseInt(this.currentSeconds / this.durationSeconds * 100);
            },
            muted() {
                return this.volume / 100 === 0;
            },
            trackInfo(){
                let trackInfo = '';
                let artists = this.artists;
                let trackMarquee = document.querySelector('.track-info .track-marquee');
                
                if(artists.length){
                    
                    for(let i = 0; i < artists.length; i++){
                        trackInfo += artists[i].name;
                        if(i < artists.length - 1){
                            trackInfo += ', ';
                        }
                    }

                    trackInfo += ` — ${this.track}`;
                    

                    if(trackInfo.length > 66 && window.innerWidth >= 768 || trackInfo.length > 40 && window.innerWidth < 768){
                        trackMarquee.classList.add('scroll');
                    } else {
                        trackMarquee.classList.remove('scroll');
                    }
                }

                return trackInfo;
            }
        },
        watch: {
            playing(value) {
                if (value) { return this.audio.play(); }
                this.audio.pause();
            },
            volume(value) {
                //this.showVolume = false;
                this.audio.volume = this.volume / 100;
            },
            file(newSrc, oldSrc){
                var self = this;
                
                if(oldSrc !== null){
                    if(newSrc !== oldSrc){
                        self.loaded = false;
                        self.playing = false;
                    }
                }
            }
        },
        methods: {
            load() {
                if (this.audio.readyState >= 2) {
                    this.loaded = true;
                    this.durationSeconds = parseInt(this.audio.duration);
                    return this.playing = this.autoPlay;
                }

                throw new Error('Failed to load sound file.');
            },
            mute() {
                if (this.muted) {
                    return this.volume = this.previousVolume;
                }

                this.previousVolume = this.volume;
                this.volume = 0;
            },
            seek(e) {
                if (!this.playing || e.target.tagName === 'SPAN') {
                    return;
                }

                const el = e.target.getBoundingClientRect();
                const seekPos = (e.clientX - el.left) / el.width;

                this.audio.currentTime = parseInt(this.audio.duration * seekPos);
            },
            stop() {
                this.playing = false;
                this.audio.currentTime = 0;
            },
            update(e) {
                this.currentSeconds = parseInt(this.audio.currentTime);
            },
            closePlayer(){
                this.$emit('closed', true);
            }
        },
        created() {
            this.innerLoop = this.loop;
        },
        mounted() {
            this.audio = this.$el.querySelectorAll('audio')[0];
            this.audio.addEventListener('timeupdate', this.update);
            this.audio.addEventListener('loadeddata', this.load);
            this.audio.addEventListener('pause', () => { this.playing = false; });
            this.audio.addEventListener('play', () => { this.playing = true; });
        }
    }

    const convertTimeHHMMSS = (val) => {
        let hhmmss = new Date(val * 1000).toISOString().substr(11, 8);

        return hhmmss.indexOf("00:") === 0 ? hhmmss.substr(3) : hhmmss;
    };
</script>
