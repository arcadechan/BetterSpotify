<template>
    <div class="row d-flex align-items-center justify-content-start mt-3">
        <div v-if="alerts.length" id="contact-alerts" class="text-center">
            <p v-if="alerts.includes('reCAPTCHA')" class="alert alert-danger" role="alert">Please solve the captcha.</p>
            <p v-if="alerts.includes('contactFailed')" class="alert alert-danger" role="alert">Uh oh! Something went wrong and your form could not be submitted. Please try again.</p>
            <p v-if="alerts.includes('contactSuccess')" class="alert alert-success" role="alert">Thanks for getting in contact!</p>
        </div>
        <div class="col-12 px-lg-5 px-md-5">
            <form id="contact-form" @submit.prevent="submit">
                <div class="contact-form">
                    <div class="form-group row">
                        <div class="col-12">          
                            <input v-model="form.name" type="text" class="form-control" id="fname" placeholder="Your name" name="name">
                        </div>
                    </div>
                    <div class="form-group row">
                    
                        <div class="col-12">
                            <input v-model="form.email" type="email" class="form-control" id="email" placeholder="Enter email *" name="email" required>
                        </div>
                    </div>
                    <div class="form-group row">
                    
                        <div class="col-12">
                            <textarea v-model="form.message" class="form-control" rows="5" id="message"  placeholder="Enter your message *" name="message" required></textarea>
                        </div>
                    </div>
                    
                    <!-- RECAPTCHA -->
                    <div id="recaptcha-container" class="d-flex justify-content-center mb-3">
						<div id="recaptcha" class="g-recaptcha" :data-sitekey="recaptcha_site_key"></div>
                    </div>

                    <div class="form-group row">        
                        <div class="col-12 col-md-4 mx-md-auto">
                            <button id="contact-form-submit" type="submit" class="btn btn-spotify font-weight-bold w-100">Submit</button>
                        </div>
                    </div>
                </div>
            </form>                  
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                submitSuccess: false,
                form: {
                    name: null,
                    email: null,
                    message: null
                },
                alerts: [],
                isSubmitting: false,
            }
        },
        props: {
            recaptcha_site_key: {
                type: String,
                required: true,
                default: ''
            }      
        },
        methods: {
            submit: function(event){
                const reCAPTCHA = event.target['g-recaptcha-response'].value;
                const self = this;
                
                this.validate(reCAPTCHA);

                this.submitSuccess = false;
                this.isSubmitting = true;

                const data = {
                    form: this.form,
                    reCAPTCHA
                };

                if(!this.alerts.includes('reCAPTCHA')){
                    axios.post('/contact', data, {
                        'Access-Control-Allow-Origin': '*',
                        'Access-Control-Allow-Methods': 'POST'
                    })
                    .then(response => {
                        this.submitSuccess = true;
                        this.alerts.push('contactSuccess')
                        this.resetForm();
                    })
                    .catch(error => {
                        console.log(error);
                        self.alerts.push('contactFailed');
                    })
                    .finally(() => {
                        this.isSubmitting = false;
                        window.grecaptcha.reset();
                    });
                }

                this.isSubmitting = false;
            },
            validate: function(reCAPTCHA){
                this.alerts = [];

                if(reCAPTCHA.length === 0) this.alerts.push('reCAPTCHA');
            },
            resetForm: function() {
				var self = this;

    			this.form = {
                    name: null,
    				email: null,
    				message: null
    			}

    		}
        },
        mounted(){

        }
    }
</script>