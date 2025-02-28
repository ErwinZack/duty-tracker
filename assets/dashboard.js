document.addEventListener("DOMContentLoaded", function() {
    const colorCircles = document.querySelectorAll(".color-circle");
    const sidebar = document.querySelector(".sidebar");
    const settingsButton = document.getElementById("settingsButton");
    const settingsSidebar = document.getElementById("settingsSidebar");
    const closeSettings = document.getElementById("closeSettings");
    const lightModeBox = document.getElementById("lightModeBox");
    const darkModeBox = document.getElementById("darkModeBox");
    const bgOptions = document.querySelectorAll(".bg-option");
    const removeBgButton = document.getElementById("removeBgImage");
    const tableHeaders = document.querySelectorAll("th"); // Select all table headers

    // Load saved sidebar color or set default
    let savedColor = localStorage.getItem("sidebarColor") || "#343a40";
    let savedTheme = localStorage.getItem("theme") || "light-mode";
    let savedBgImage = localStorage.getItem("bgImage");

    // 📌 Function to Apply Sidebar & Table Header Color
    function applySidebarColor(color) {
        if (document.body.classList.contains("light-mode")) {
            document.documentElement.style.setProperty("--theme-color", color);
            localStorage.setItem("sidebarColor", color);
            
            // Apply the same color to table headers
            tableHeaders.forEach(th => {
                th.style.backgroundColor = color;
                th.style.color = "#fff"; // Ensure text remains readable
            });
        }
    }

    // 📌 Function to Apply Theme Mode
    function applyTheme(theme) {
        document.body.classList.remove("light-mode", "dark-mode");
        document.body.classList.add(theme);
        localStorage.setItem("theme", theme);

        if (theme === "dark-mode") {
            document.documentElement.style.setProperty("--theme-color", "#222"); // Dark Sidebar

            // Dark mode: Change table headers to a dark color
            tableHeaders.forEach(th => {
                th.style.backgroundColor = "#333";
                th.style.color = "#fff";
            });
        } else {
            applySidebarColor(savedColor); // Restore Saved Sidebar Color in Light Mode
        }

        // Sidebar Text Color Always White
        document.documentElement.style.setProperty("--sidebar-text-color", "#fff");

        // Toggle active state
        lightModeBox.classList.toggle("active", theme === "light-mode");
        darkModeBox.classList.toggle("active", theme === "dark-mode");
    }

    // 📌 Apply saved settings
    applyTheme(savedTheme);
    applySidebarColor(savedColor);

    if (savedBgImage) {
        document.body.style.backgroundImage = `url('../assets/image/${savedBgImage}')`;
    }

    // 📌 Change Sidebar & Table Header Color on Selection
    colorCircles.forEach(circle => {
        circle.addEventListener("click", function() {
            let selectedColor = this.getAttribute("data-color");
            savedColor = selectedColor;
            applySidebarColor(selectedColor);
        });
    });

    // 📌 Light & Dark Mode Toggle
    lightModeBox.addEventListener("click", () => applyTheme("light-mode"));
    darkModeBox.addEventListener("click", () => applyTheme("dark-mode"));

    // 📌 Background Image Selection
    bgOptions.forEach(img => {
        img.addEventListener("click", function() {
            let selectedImage = this.getAttribute("data-image");
            document.body.style.backgroundImage = `url('../assets/image/${selectedImage}')`;
            localStorage.setItem("bgImage", selectedImage);
        });
    });

    // 📌 Remove Background Image
    removeBgButton.addEventListener("click", function() {
        document.body.style.backgroundImage = "none";
        localStorage.removeItem("bgImage");
    });

    // 📌 Settings Panel Toggle
    settingsButton.addEventListener("click", function() {
        settingsSidebar.classList.add("open");
    });

    closeSettings.addEventListener("click", function() {
        settingsSidebar.classList.remove("open");
    });
});
