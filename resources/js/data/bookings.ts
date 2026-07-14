export interface Booking {
    id: number;
    customerName: string;
    serviceType: string;
    technicianName: string;
    technicianAvatar: string;
    date: string;
    time: string;
    status: 'pending' | 'active' | 'completed' | 'cancelled';
    cost: number;
    location: string;
    notes?: string;
}

export interface Order {
    id: string;
    customerName: string;
    productName: string;
    quantity: number;
    totalPrice: number;
    status: 'processing' | 'shipped' | 'delivered' | 'cancelled';
    orderDate: string;
    shippingAddress: string;
    trackingNumber?: string;
}

export const mockBookings: Booking[] = [
    {
        id: 101,
        customerName: 'Alice Johnson',
        serviceType: 'Annual Solar Health Audit',
        technicianName: 'Marcus Vance',
        technicianAvatar: 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&q=80&w=150',
        date: '2026-06-02',
        time: '10:00 AM',
        status: 'active',
        cost: 120,
        location: '124 Oakwood Ave, San Francisco, CA',
        notes: 'Inverter displaying a flashing red light and efficiency seems slightly reduced.'
    },
    {
        id: 102,
        customerName: 'Robert Chen',
        serviceType: 'Battery Storage Installation',
        technicianName: 'Darnell Washington',
        technicianAvatar: 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&q=80&w=150',
        date: '2026-05-30',
        time: '02:00 PM',
        status: 'pending',
        cost: 450,
        location: '588 Horizon Blvd, San Francisco, CA',
        notes: 'Integrating 10kWh SolarLink Battery Wall with pre-existing solar system.'
    },
    {
        id: 103,
        customerName: 'Sarah Jenkins',
        serviceType: 'Solar Panel Panel Cleaning',
        technicianName: 'Elena Rostova',
        technicianAvatar: 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&q=80&w=150',
        date: '2026-05-24',
        time: '09:00 AM',
        status: 'completed',
        cost: 85,
        location: '72 Pine St, San Francisco, CA',
        notes: 'Heavy dust buildup on panels from recent dry winds.'
    },
    {
        id: 104,
        customerName: 'David Lee',
        serviceType: 'Inverter Replacement',
        technicianName: 'Kaito Tanaka',
        technicianAvatar: 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&q=80&w=150',
        date: '2026-05-15',
        time: '11:30 AM',
        status: 'cancelled',
        cost: 220,
        location: '900 Sunset Way, San Francisco, CA',
        notes: 'Cancel reason: Decided to upgrade entire system later.'
    }
];

export const mockOrders: Order[] = [
    {
        id: 'SL-8849',
        customerName: 'Timothy Vance',
        productName: 'AeroVolt 450W Monocrystalline Panel',
        quantity: 6,
        totalPrice: 1734,
        status: 'shipped',
        orderDate: '2026-05-26',
        shippingAddress: '430 Mission District, San Francisco, CA',
        trackingNumber: 'TRK-SL-90924901'
    },
    {
        id: 'SL-7429',
        customerName: 'Clara Oswald',
        productName: 'SolarLink Max 10kWh Battery Wall',
        quantity: 1,
        totalPrice: 3499,
        status: 'processing',
        orderDate: '2026-05-28',
        shippingAddress: '12 Emerald Bay Dr, San Francisco, CA'
    },
    {
        id: 'SL-6512',
        customerName: 'Benjamin Franklin',
        productName: 'MPPT Pro 60A Charge Controller',
        quantity: 2,
        totalPrice: 298,
        status: 'delivered',
        orderDate: '2026-05-20',
        shippingAddress: '1776 Lightning Ridge, Philadelphia, PA',
        trackingNumber: 'TRK-SL-3382942'
    }
];
