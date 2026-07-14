export interface Notification {
    id: number;
    title: string;
    description: string;
    timestamp: string;
    type: 'info' | 'success' | 'warning' | 'error';
    read: boolean;
}

export const mockNotifications: Notification[] = [
    {
        id: 1,
        title: 'Technician Assigned',
        description: 'Marcus Vance has been assigned to your Solar Health Audit scheduled for June 2nd.',
        timestamp: '10 mins ago',
        type: 'success',
        read: false
    },
    {
        id: 2,
        title: 'Grid Power Restored',
        description: 'Your solar system has successfully switched back to grid-parallel operations.',
        timestamp: '1 hour ago',
        type: 'info',
        read: false
    },
    {
        id: 3,
        title: 'Battery Reserve Warning',
        description: 'Solar battery capacity dropped below 15%. Consider reducing heavy household load.',
        timestamp: '4 hours ago',
        type: 'warning',
        read: true
    },
    {
        id: 4,
        title: 'Order Dispatched',
        description: 'Your order SL-8849 with 6 AeroVolt Panels has been shipped and is en route.',
        timestamp: '1 day ago',
        type: 'success',
        read: true
    },
    {
        id: 5,
        title: 'Inverter Communication Fault',
        description: 'System lost connectivity with hybrid inverter #1. Auto-retrying handshake.',
        timestamp: '2 days ago',
        type: 'error',
        read: true
    }
];
