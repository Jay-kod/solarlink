import { ref } from 'vue'

export type AlertType = 'info' | 'success' | 'warning' | 'danger' | 'confirm'

interface AlertOptions {
    title: string
    message: string
    type?: AlertType
    confirmText?: string
    cancelText?: string
    onConfirm?: () => void
    onCancel?: () => void
}

const isOpen = ref(false)
const alertState = ref<AlertOptions>({
    title: '',
    message: '',
    type: 'info',
    confirmText: 'OK',
    cancelText: 'Cancel'
})

export const useAlert = () => {
    const showAlert = (options: AlertOptions) => {
        alertState.value = {
            type: 'info',
            confirmText: 'OK',
            cancelText: 'Cancel',
            ...options
        }
        isOpen.value = true
    }

    const confirmAlert = (message: string, title = 'Confirm Action', options?: Partial<AlertOptions>): Promise<boolean> => {
        return new Promise((resolve) => {
            showAlert({
                title,
                message,
                type: 'confirm',
                confirmText: 'Confirm',
                ...options,
                onConfirm: () => {
                    isOpen.value = false
                    resolve(true)
                },
                onCancel: () => {
                    isOpen.value = false
                    resolve(false)
                }
            })
        })
    }
    
    const infoAlert = (message: string, title = 'Information', options?: Partial<AlertOptions>) => {
        showAlert({
            title,
            message,
            type: 'info',
            ...options,
            onConfirm: () => {
                isOpen.value = false
                if (options?.onConfirm) options.onConfirm()
            }
        })
    }

    const closeAlert = () => {
        isOpen.value = false
        if (alertState.value.onCancel) {
            alertState.value.onCancel()
        }
    }

    const handleConfirm = () => {
        if (alertState.value.onConfirm) {
            alertState.value.onConfirm()
        } else {
            isOpen.value = false
        }
    }

    return {
        isOpen,
        alertState,
        showAlert,
        confirmAlert,
        infoAlert,
        closeAlert,
        handleConfirm
    }
}
