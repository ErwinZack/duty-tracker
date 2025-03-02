document.addEventListener('DOMContentLoaded', function() {
    // Toggle sidebar on mobile
    const menuToggle = document.getElementById('menu-toggle');
    const sidebar = document.querySelector('.sidebar');
    const mainContent = document.querySelector('.main-content');
    
    if (menuToggle && sidebar && mainContent) {
        menuToggle.addEventListener('click', function() {
            sidebar.classList.toggle('active');
            mainContent.classList.toggle('expanded');
        });
    }
    
    // Close sidebar when clicking the close button
    const closeSidebar = document.querySelector('.close-sidebar');
    if (closeSidebar && sidebar) {
        closeSidebar.addEventListener('click', function() {
            sidebar.classList.remove('active');
            if (mainContent) {
                mainContent.classList.add('expanded');
            }
        });
    }
    
    // Settings panel toggle
    const settingsButton = document.getElementById('settingsButton');
    const settingsSidebar = document.getElementById('settingsSidebar');
    const closeSettings = document.getElementById('closeSettings');
    
    if (settingsButton && settingsSidebar && closeSettings) {
        settingsButton.addEventListener('click', function() {
            settingsSidebar.classList.add('active');
        });
        
        closeSettings.addEventListener('click', function() {
            settingsSidebar.classList.remove('active');
        });
    }
    
    // Theme selection
    const themeBoxes = document.querySelectorAll('.theme-box');
    
    themeBoxes.forEach(box => {
        box.addEventListener('click', function() {
            // Remove active class from all boxes
            themeBoxes.forEach(b => b.classList.remove('active'));
            
            // Add active class to clicked box
            this.classList.add('active');
            
            // Get theme from data attribute
            const theme = this.getAttribute('data-theme');
            
            // Apply theme (body class change)
            document.body.className = theme;
            
            // Save preference to localStorage
            localStorage.setItem('preferred-theme', theme);
        });
    });
    
    // Color theme selection
    const colorCircles = document.querySelectorAll('.color-circle');
    
    colorCircles.forEach(circle => {
        circle.addEventListener('click', function() {
            // Remove active class from all circles
            colorCircles.forEach(c => c.classList.remove('active'));
            
            // Add active class to clicked circle
            this.classList.add('active');
            
            // Get color from data attribute
            const color = this.getAttribute('data-color');
            
            // Apply color to sidebar
            if (sidebar) {
                sidebar.style.background = color;
            }
            
            // Save preference to localStorage
            localStorage.setItem('sidebar-color', color);
        });
    });
    
    // Background image selection
    const bgOptions = document.querySelectorAll('.bg-option');
    const removeBgBtn = document.getElementById('removeBgImage');
    
    bgOptions.forEach(option => {
        option.addEventListener('click', function() {
            // Remove active class from all options
            bgOptions.forEach(o => o.classList.remove('active'));
            
            // Add active class to clicked option
            this.classList.add('active');
            
            // Get image from data attribute
            const image = this.getAttribute('data-image');
            
            // Apply background to main-content
            if (mainContent) {
                mainContent.style.backgroundImage = `url('../assets/image/${image}')`;
                mainContent.style.backgroundSize = 'cover';
                mainContent.style.backgroundAttachment = 'fixed';
            }
            
            // Save preference to localStorage
            localStorage.setItem('bg-image', image);
        });
    });
    
    if (removeBgBtn) {
        removeBgBtn.addEventListener('click', function() {
            // Remove background from main-content
            if (mainContent) {
                mainContent.style.backgroundImage = 'none';
                mainContent.style.background = '#f5f7fa';
            }
            
            // Remove active class from all options
            bgOptions.forEach(o => o.classList.remove('active'));
            
            // Remove preference from localStorage
            localStorage.removeItem('bg-image');
        });
    }
    
    // Load saved preferences from localStorage
    const loadSavedPreferences = () => {
        // Load theme
        const savedTheme = localStorage.getItem('preferred-theme');
        if (savedTheme) {
            document.body.className = savedTheme;
            
            // Update active theme box
            themeBoxes.forEach(box => {
                if (box.getAttribute('data-theme') === savedTheme) {
                    box.classList.add('active');
                } else {
                    box.classList.remove('active');
                }
            });
        }
        
        // Load sidebar color
        const savedColor = localStorage.getItem('sidebar-color');
        if (savedColor && sidebar) {
            sidebar.style.background = savedColor;
            
            // Update active color circle
            colorCircles.forEach(circle => {
                if (circle.getAttribute('data-color') === savedColor) {
                    circle.classList.add('active');
                } else {
                    circle.classList.remove('active');
                }
            });
        }
        
        // Load background image
        const savedBgImage = localStorage.getItem('bg-image');
        if (savedBgImage && mainContent) {
            mainContent.style.backgroundImage = `url('../assets/image/${savedBgImage}')`;
            mainContent.style.backgroundSize = 'cover';
            mainContent.style.backgroundAttachment = 'fixed';
        }
        
        // Check if sidebar should be active based on screen size
        const checkSidebarState = () => {
            if (window.innerWidth <= 991) {
                sidebar.classList.remove('active');
                mainContent.classList.add('expanded');
            } else {
                sidebar.classList.remove('active');
                mainContent.classList.remove('expanded');
            }
        };
        
        // Initialize sidebar state
        checkSidebarState();
        
        // Update on window resize
        window.addEventListener('resize', checkSidebarState);
    };
    
    // Call function to load saved preferences
    loadSavedPreferences();
});
function formatDate(date) {
    const options = { month: "short", day: "2-digit", year: "numeric" };
    return date.toLocaleDateString("en-US", options);
}

// Get today's date
const today = new Date();
const startDate = formatDate(today);
const endDate = formatDate(new Date(today.setDate(today.getDate() + 7)));

// Set input value
document.getElementById("dateRange").value = `${startDate} - ${endDate}`;