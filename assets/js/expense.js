const app = Vue.createApp({
    data() {
        return {
            expenses: [],
            currentPage: 1, // Current page number
            itemsPerPage: 5, // Number of items to show per page
            categories: [],
            subCategories: [],
            remainingBudgets: [],
            showEditModal: false,
            newExpense: {
                amount: '',
                expense_date: '',
                category_id: '',
                sub_category_id: '',
                description: '',
            },
            editedExpense: {
                expense_transaction_id: '',
                amount: '',
                expense_date: '',
                category_id: '',
                sub_category_id: '',
                description: '',
            },
        };
    },
    computed: {
        // Tính toán danh sách expenses đã phân trang
        paginatedExpenses() {
            const start = (this.currentPage - 1) * this.itemsPerPage;
            const end = start + this.itemsPerPage;
            return this.expenses.slice(start, end);
        },
        // Tính toán tổng số trang dựa trên tổng số expenses
        totalPages() {
            return Math.ceil(this.expenses.length / this.itemsPerPage);
        },
    },
    methods: {
        async fetchExpenses() {
            try {
                const response = await fetch('backend/api_expense.php?action=fetch');
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                const data = await response.json();
                this.expenses = data.expenses; // Lấy danh sách expenses từ phản hồi
            } catch (error) {
                console.error("Error fetching expenses:", error.message);
            }
        },
        async fetchCategories() {
            try {
                const response = await fetch('backend/fetch_categories.php');
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                const data = await response.json();
                this.categories = data;
            } catch (error) {
                console.error("Error fetching categories:", error.message);
            }
        },
        async fetchSubCategories(categoryId) {
            try {
                const response = await fetch(`backend/fetch_sub_categories.php?category_id=${categoryId}`);
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                const data = await response.json();
                this.subCategories = data; // Lưu danh sách sub-categories
            } catch (error) {
                console.error("Error fetching sub-categories:", error.message);
            }
        },
        async fetchRemainingBudgets() {
            try {
                const response = await fetch('backend/fetch_remaining_budgets.php');
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                const data = await response.json();
                this.remainingBudgets = data;
            } catch (error) {
                console.error("Error fetching remaining budgets:", error.message);
            }
        },
        async addExpense() {
            try {
                const response = await fetch('backend/api_expense.php?action=add', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(this.newExpense),
                });
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                const result = await response.json();
                if (result.status !== 'success') {
                    throw new Error(result.message || 'Failed to add expense');
                }
                await this.fetchExpenses(); // Cập nhật danh sách
                alert('Expense added successfully!');
                this.newExpense = {
                    amount: '',
                    expense_date: '',
                    category_id: '',
                    sub_category_id: '',
                    description: '',
                };
            } catch (error) {
                console.error("Error adding expense:", error.message);
            }
        },
        openEditModal(expense) {
            this.editedExpense = { ...expense }; // Gán dữ liệu expense vào editedExpense
            this.fetchSubCategories(expense.category_id); // Tải sub-categories cho category hiện tại
            this.showEditModal = true; // Hiển thị modal chỉnh sửa
        },
        closeEditModal() {
            this.showEditModal = false; // Đóng modal
        },
        async saveEditedExpense() {
            try {
                const response = await fetch('backend/api_expense.php?action=update', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(this.editedExpense),
                });
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                const result = await response.json();
                if (result.status !== 'success') {
                    throw new Error(result.message || 'Failed to update expense');
                }
                alert('Expense updated successfully!');
                this.showEditModal = false; // Đóng modal
                await this.fetchExpenses(); // Cập nhật danh sách
                await this.fetchRemainingBudgets();
            } catch (error) {
                console.error('Error updating expense:', error.message);
            }
        },
        async deleteExpense(expenseId) {
            try {
                if (confirm('Are you sure you want to delete this expense?')) {
                    const response = await fetch('backend/api_expense.php?action=delete', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({ expense_transaction_id: expenseId }),
                    });
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    const result = await response.json();
                    if (result.status !== 'success') {
                        throw new Error(result.message || 'Failed to delete expense');
                    }
                    await this.fetchExpenses(); // Cập nhật danh sách
                    await this.fetchRemainingBudgets();
                    alert('Expense deleted successfully!');
                }
            } catch (error) {
                console.error("Error deleting expense:", error.message);
            }
        },
        changePage(page) {
            if (page >= 1 && page <= this.totalPages) {
                this.currentPage = page;
            }
        }
    },
    async mounted() {
        await this.fetchExpenses();
        await this.fetchCategories();
        await this.fetchRemainingBudgets();
        console.log("Current Page:", this.currentPage);
        console.log("Total Pages:", this.totalPages);
        console.log("Filtered Expenses:", this.filteredExpenses);
    },
});
app.mount('#app');
