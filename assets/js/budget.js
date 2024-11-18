const app = Vue.createApp({
    data() {
        return {
            budgets: [], // Danh sách ngân sách
            categories: [], // Danh sách danh mục
            showEditModal: false,
            form: {
                budget_id: null,
                amount: null,
                category_id: null,
                start_date: null,
                end_date: null,
            },
        };
    },
    methods: {
        async fetchBudgets() {
            try {
                const response = await fetch("backend/api_budget.php");
                this.budgets = await response.json();
            } catch (error) {
                console.error("Error fetching budgets:", error);
            }
        },
        async fetchCategories() {
            try {
                const response = await fetch("backend/fetch_categories.php");
                this.categories = await response.json();
            } catch (error) {
                console.error("Error fetching categories:", error);
            }
        },
       async addBudget() {
            try {
                const response = await fetch("backend/api_budget.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify(this.form),
                });

                const result = await response.json();
                if (result.status === "success") {
                    alert(result.message);
                    this.fetchBudgets(); // Tải lại danh sách ngân sách
                    this.resetForm(); // Đặt lại form
                } else {
                    alert(`Error: ${result.message}`);
                }
            } catch (error) {
                console.error("Error adding budget:", error);
            }
        },
        async deleteBudget(budget_id) {
            try {
                const response = await fetch(`backend/api_budget.php?id=${budget_id}`, { method: "GET" });
                const result = await response.json();
                if (result.status === "success") {
                    alert(result.message);
                    this.fetchBudgets(); // Tải lại danh sách ngân sách
                } else {
                    alert(`Error: ${result.message}`);
                }
            } catch (error) {
                console.error("Error deleting budget:", error);
            }
        },
        async updateBudget() {
            try {
                const response = await fetch("backend/api_budget.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify(this.form),
                });

                const result = await response.json();
                if (result.status === "success") {
                    alert(result.message);
                    this.fetchBudgets(); // Tải lại danh sách ngân sách
                    this.resetForm(); // Đặt lại form
                    const modal = bootstrap.Modal.getInstance(document.getElementById("editBudgetModal"));
                    modal.hide(); // Ẩn modal chỉnh sửa
                } else {
                    alert(`Error: ${result.message}`);
                }
            } catch (error) {
                console.error("Error updating budget:", error);
            }
        },

        editBudget(budget) {
            // Sao chép thông tin ngân sách vào form
            this.form = {
                budget_id: budget.budget_id,
                amount: budget.amount,
                category_id: budget.category_id,
                start_date: budget.start_date,
                end_date: budget.end_date,
            };
            // Hiển thị modal
            const modal = new bootstrap.Modal(document.getElementById("editBudgetModal"));
            modal.show();
            console.log("Editing budget:", budget); // Log để kiểm tra sự kiện
        },

    },
    mounted() {
        this.fetchBudgets();
        this.fetchCategories();
    },
});

app.mount("#app");



