const app = Vue.createApp({
    data() {
        return {
            form: {
                username: '',
                email: '',
                password: '',
                confirm_password: ''
            },
            errors: {
                username: '',
                email: '',
                password: '',
                confirm_password: ''
            },
            error: '', // Hiển thị lỗi chung
            success: '' // Hiển thị thông báo thành công
        };
    },
    computed: {
        isFormValid() {
            return (
                !this.errors.username &&
                !this.errors.email &&
                !this.errors.password &&
                !this.errors.confirm_password &&
                this.form.username &&
                this.form.email &&
                this.form.password &&
                this.form.confirm_password
            );
        }
    },
    methods: {
        validateField(field) {
            switch (field) {
                case 'username':
                    this.errors.username = this.form.username
                        ? ''
                        : 'Username is required.';
                    break;
                case 'email':
                    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    this.errors.email = this.form.email
                        ? emailRegex.test(this.form.email)
                            ? ''
                            : 'Email must contain "@".'
                        : 'Email is required.';
                    break;
                case 'password':
                    const passwordRegex = /^(?=.*[!@#$%^&*])(?=.*\d).{6,}$/;
                    this.errors.password = passwordRegex.test(this.form.password)
                        ? ''
                        : 'Password must be at least 6 characters, include a special character and a number.';
                    break;
                case 'confirm_password':
                    this.errors.confirm_password =
                        this.form.confirm_password === this.form.password
                            ? ''
                            : 'Passwords do not match.';
                    break;
            }
        },
        async submitForm() {
            this.error = '';
            this.success = '';
            
            // Kiểm tra toàn bộ form
            this.validateField('username');
            this.validateField('email');
            this.validateField('password');
            this.validateField('confirm_password');
            
            if (!this.isFormValid) {
                this.error = 'Please fix the errors in the form.';
                return;
            }

            try {
                const response = await fetch('backend/signup.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(this.form)
                });

                const result = await response.json();

                if (result.status === 'success') {
                    this.success = result.message;
                    // Reset form
                    this.form.username = '';
                    this.form.email = '';
                    this.form.password = '';
                    this.form.confirm_password = '';
                } else {
                    this.error = result.message;
                }
            } catch (error) {
                this.error = 'An error occurred. Please try again.';
            }
        }
    }
});

app.mount('#app');
