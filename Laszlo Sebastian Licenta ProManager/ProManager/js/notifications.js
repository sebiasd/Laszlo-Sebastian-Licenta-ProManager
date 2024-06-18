document.addEventListener('DOMContentLoaded', function() {
    const notifications = document.querySelectorAll('.notification');

    notifications.forEach(notification => {
        setTimeout(() => {
            notification.classList.add('visible');
        }, 100); 
        setTimeout(() => {
            notification.classList.remove('visible');
            setTimeout(() => {
                notification.parentNode.removeChild(notification);
            }, 500); 
        }, 5000); 
    });
});
