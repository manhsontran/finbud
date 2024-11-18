const app = Vue.createApp({
    data() {
        return {
            username: '',
            password: '',
            loading: false,
            errorMessage: ''
        };
    },
    methods: {
        async submitForm() {
            this.loading = true;
            this.errorMessage = '';
            
            try {
                const response = await fetch('backend/login_api.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: new URLSearchParams({
                        username: this.username,
                        password: this.password
                    })
                });
                
                const result = await response.json();

                if (result.status === "success") {
                    window.location.href = 'index.html';
                } else {
                    this.errorMessage = result.message;
                }
            } catch (error) {
                this.errorMessage = 'An error occurred. Please try again.';
            } finally {
                this.loading = false;
            }
        }
    }
});

app.mount('#app');
