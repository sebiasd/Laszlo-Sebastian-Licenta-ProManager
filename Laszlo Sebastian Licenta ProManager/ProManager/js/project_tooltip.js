document.addEventListener('DOMContentLoaded', function() {
    const progressBar = document.querySelector('.progress-bar');
    const projectTooltip = document.getElementById('projectTooltip');

    if (progressBar) {
        progressBar.addEventListener('mouseenter', function(event) {
            const proiect = progressBar.dataset.proiect; // Use progressBar instead of event.target
            const done = progressBar.dataset.done;
            const total = progressBar.dataset.total;
            const remaining = total - done;

            projectTooltip.style.display = 'block';
            projectTooltip.style.left = event.pageX + 'px';
            projectTooltip.style.top = event.pageY + 'px';
            projectTooltip.innerHTML = `<strong>${proiect}</strong><br>Ore lucrate: ${done}<br>Ore rămase: <span style="color: red;">${remaining}</span>`;
        });

        progressBar.addEventListener('mouseleave', function() {
            projectTooltip.style.display = 'none';
        });
    }

    document.querySelectorAll('.progress-task').forEach(function(element) {
        element.addEventListener('mouseenter', function(event) {
            const tooltip = document.getElementById('taskTooltip');
            const task = element.dataset.task; // Use element instead of event.target
            const done = element.dataset.done;
            const total = element.dataset.total;
            const remaining = total - done;

            tooltip.style.display = 'block';
            tooltip.style.left = event.pageX + 'px';
            tooltip.style.top = event.pageY + 'px';
            tooltip.innerHTML = `<strong>${task}</strong><br>Ore lucrate: ${done}<br>Ore rămase: <span style="color: red;">${remaining}</span>`;
        });

        element.addEventListener('mouseleave', function() {
            const tooltip = document.getElementById('taskTooltip');
            tooltip.style.display = 'none';
        });
    });
});
