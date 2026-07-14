export interface Technician {
    id: number;
    name: string;
    avatar: string;
    rating: number;
    reviewCount: number;
    distance: string;
    skills: string[];
    pricePerHour: number;
    status: 'online' | 'offline' | 'busy';
    experience: string;
    lat: number;
    lng: number;
    eta: string;
}

export const mockTechnicians: Technician[] = [
    {
        id: 1,
        name: 'Marcus Vance',
        avatar: 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&q=80&w=150',
        rating: 4.9,
        reviewCount: 142,
        distance: '1.2 miles',
        skills: ['Inverter Repair', 'Battery Storage Setup', 'Panel Cleaning'],
        pricePerHour: 85,
        status: 'online',
        experience: '6 years exp',
        lat: 37.7749,
        lng: -122.4194,
        eta: '12 mins'
    },
    {
        id: 2,
        name: 'Elena Rostova',
        avatar: 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&q=80&w=150',
        rating: 4.8,
        reviewCount: 96,
        distance: '2.5 miles',
        skills: ['Solar Panel Installation', 'Electrical Wiring', 'System Diagnostics'],
        pricePerHour: 95,
        status: 'online',
        experience: '4 years exp',
        lat: 37.7833,
        lng: -122.4167,
        eta: '18 mins'
    },
    {
        id: 3,
        name: 'Darnell Washington',
        avatar: 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&q=80&w=150',
        rating: 4.95,
        reviewCount: 210,
        distance: '3.1 miles',
        skills: ['Tesla Powerwall Certified', 'Commercial Solar', 'Grid-Tie Systems'],
        pricePerHour: 110,
        status: 'busy',
        experience: '8 years exp',
        lat: 37.7699,
        lng: -122.4468,
        eta: '25 mins'
    },
    {
        id: 4,
        name: 'Kaito Tanaka',
        avatar: 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&q=80&w=150',
        rating: 4.7,
        reviewCount: 68,
        distance: '4.8 miles',
        skills: ['Microinverter Setup', 'Roof Leak Proofing', 'Safety Audits'],
        pricePerHour: 80,
        status: 'online',
        experience: '3 years exp',
        lat: 37.7599,
        lng: -122.4368,
        eta: '32 mins'
    },
    {
        id: 5,
        name: 'Sophia Martinez',
        avatar: 'https://images.unsplash.com/photo-1438761681033-6461ffad8d80?auto=format&fit=crop&q=80&w=150',
        rating: 4.85,
        reviewCount: 88,
        distance: '5.2 miles',
        skills: ['Off-Grid Solar Design', 'Smart Home Integration', 'Maintenance Plans'],
        pricePerHour: 90,
        status: 'offline',
        experience: '5 years exp',
        lat: 37.7899,
        lng: -122.4068,
        eta: '45 mins'
    }
];
