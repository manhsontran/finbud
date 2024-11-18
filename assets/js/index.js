const { createApp } = Vue;

createApp({
    data() {
        return {
            navLinks: [
                { name: "Home", url: "dashboard.html", icon: "fas fa-home" },
                { name: "Expenses", url: "expensepage.html", icon: "fas fa-wallet" },
                { name: "Budgets", url: "budget.html", icon: "fas fa-chart-line" },
                { name: "Goals", url: "finance_goalpage.html", icon: "fas fa-bullseye" },
                { name: "Income", url: "add_income.html", icon: "fas fa-coins" },
                { name: "Reports", url: "reportpage.html", icon: "fas fa-file-alt" },
            ],
            features: [
                {
                    title: "Expense Tracking",
                    icon: "fas fa-wallet",
                    description: "Easily log every transaction, categorize your spending, and keep track of where your money is going. This helps you identify unnecessary expenses and make adjustments to stay within your budget.",
                },
                {
                    title: "Budget Creation",
                    icon: "fas fa-chart-line",
                    description: "Create personalized budgets for various expense categories. FinBud allows you to set monthly or weekly limits, helping you prevent overspending and encouraging smart saving habits.",
                },
                {
                    title: "Financial Goal Setting",
                    icon: "fas fa-bullseye",
                    description: "Define and monitor your financial goals, whether saving for a vacation, a new gadget, or an emergency fund. With FinBud, track progress over time and stay motivated to reach your targets.",
                },
                {
                    title: "Spending Pattern Visualization",
                    icon: "fas fa-chart-pie",
                    description: "Gain insights into your spending behavior through visual charts and graphs. FinBud helps you understand patterns, so you can adjust your habits and make better financial decisions.",
                },
                {
                    title: "Income Tracking",
                    icon: "fas fa-money-bill-wave",
                    description: "Record all your income sources, including salary, side jobs, or passive income, to have a clear view of your cash flow and make adjustments where necessary.",
                },
                {
                    title: "Detailed Financial Reports",
                    icon: "fas fa-file-alt",
                    description: "Generate monthly or quarterly reports to review your overall financial health. These reports make it easier to identify areas for improvement and to celebrate your achievements.",
                },
            ],
        };
    },
}).mount("#app");
