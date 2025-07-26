console.log("navjs.js loaded");

document.addEventListener('DOMContentLoaded', () => {
    const themeToggle = document.getElementById('changeColorTheme');
    const savedTheme = localStorage.getItem('theme');
    if (savedTheme) {
        document.documentElement.setAttribute('data-bs-theme', savedTheme);
        document.getElementById('themeLabel').textContent = (savedTheme === 'dark' ? 'Light' : 'Dark');
        themeToggle.checked = (savedTheme === 'light');  
    }

    if (themeToggle) {
        themeToggle.addEventListener('click', () => {
            const currentTheme = document.documentElement.getAttribute('data-bs-theme');
            console.log("changeColorTheme value", themeToggle.value);
            if (themeToggle.checked == true)  {
                var newTheme = 'light';
            }
            else {
                var newTheme = 'dark';
            }
            document.documentElement.setAttribute('data-bs-theme', newTheme);
            document.getElementById('themeLabel').textContent = (newTheme === 'dark' ? 'Light' : 'Dark');
            localStorage.setItem('theme', newTheme);
        });
    }
});

