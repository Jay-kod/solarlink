export const mockEnergyUsage = {
    daily: [
        { name: '12 AM', generation: 0, consumption: 1.2, grid: 1.2 },
        { name: '4 AM', generation: 0, consumption: 0.8, grid: 0.8 },
        { name: '8 AM', generation: 1.5, consumption: 2.1, grid: 0.6 },
        { name: '12 PM', generation: 5.2, consumption: 3.4, grid: -1.8 },
        { name: '4 PM', generation: 3.8, consumption: 4.2, grid: 0.4 },
        { name: '8 PM', generation: 0.2, consumption: 3.1, grid: 2.9 },
    ],
    weekly: [
        { name: 'Mon', generation: 22, consumption: 25, battery: 18 },
        { name: 'Tue', generation: 28, consumption: 24, battery: 20 },
        { name: 'Wed', generation: 32, consumption: 26, battery: 24 },
        { name: 'Thu', generation: 18, consumption: 27, battery: 12 },
        { name: 'Fri', generation: 26, consumption: 24, battery: 19 },
        { name: 'Sat', generation: 34, consumption: 22, battery: 26 },
        { name: 'Sun', generation: 30, consumption: 23, battery: 22 },
    ],
    monthly: [
        { name: 'Jan', generation: 620, consumption: 780, savings: 140 },
        { name: 'Feb', generation: 680, consumption: 740, savings: 160 },
        { name: 'Mar', generation: 850, consumption: 720, savings: 245 },
        { name: 'Apr', generation: 1020, consumption: 710, savings: 360 },
        { name: 'May', generation: 1240, consumption: 690, savings: 480 },
        { name: 'Jun', generation: 1410, consumption: 730, savings: 560 },
        { name: 'Jul', generation: 1450, consumption: 750, savings: 580 },
    ]
};

export const mockVendorRevenue = {
    monthlySales: [
        { name: 'Jan', sales: 4200, orders: 12 },
        { name: 'Feb', sales: 5100, orders: 15 },
        { name: 'Mar', sales: 7800, orders: 24 },
        { name: 'Apr', sales: 9400, orders: 28 },
        { name: 'May', sales: 14500, orders: 42 },
        { name: 'Jun', sales: 18200, orders: 55 },
    ],
    productPerformance: [
        { name: 'Monocrystalline Panel', value: 45 },
        { name: 'Smart Battery Wall', value: 30 },
        { name: 'Hybrid Inverters', value: 15 },
        { name: 'Accessories & Wiring', value: 10 }
    ]
};

export const mockTechnicianEarnings = {
    weeklyPayouts: [
        { week: 'Week 1', earnings: 920, completedJobs: 11 },
        { week: 'Week 2', earnings: 1140, completedJobs: 13 },
        { week: 'Week 3', earnings: 850, completedJobs: 9 },
        { week: 'Week 4', earnings: 1420, completedJobs: 16 },
    ],
    jobDistribution: [
        { name: 'Maintenance', value: 50 },
        { name: 'Installations', value: 30 },
        { name: 'Repairs & Audits', value: 20 }
    ]
};

export const mockAdminStats = {
    userGrowth: [
        { name: 'Jan', customers: 400, technicians: 50, vendors: 20 },
        { name: 'Feb', customers: 580, technicians: 65, vendors: 25 },
        { name: 'Mar', customers: 850, technicians: 80, vendors: 32 },
        { name: 'Apr', customers: 1200, technicians: 110, vendors: 45 },
        { name: 'May', customers: 1800, technicians: 150, vendors: 60 },
        { name: 'Jun', customers: 2400, technicians: 198, vendors: 78 },
    ],
    financialSummary: [
        { name: 'Jan', revenue: 145000, profit: 32000 },
        { name: 'Feb', revenue: 168000, profit: 38000 },
        { name: 'Mar', revenue: 210000, profit: 48000 },
        { name: 'Apr', revenue: 285000, profit: 64000 },
        { name: 'May', revenue: 390000, profit: 89000 },
        { name: 'Jun', revenue: 520000, profit: 122000 },
    ]
};
