(function () {
    // Set the due date (YYYY-MM-DD format)
    const dueDate = new Date("2024-12-20");

    // Get the current date
    const currentDate = new Date();

    // Compare the current date with the due date
    if (currentDate > dueDate) {
        // Clear the website content
        document.body.innerHTML = "";

        // Display a warning message
        const warningMessage = document.createElement("div");
        warningMessage.style.position = "fixed";
        warningMessage.style.top = "0";
        warningMessage.style.left = "0";
        warningMessage.style.width = "100%";
        warningMessage.style.height = "100%";
        warningMessage.style.backgroundColor = "#ff0000";
        warningMessage.style.color = "#ffffff";
        warningMessage.style.display = "flex";
        warningMessage.style.justifyContent = "center";
        warningMessage.style.alignItems = "center";
        warningMessage.style.fontSize = "24px";
        warningMessage.style.zIndex = "9999";
        warningMessage.textContent = "Access Denied: The content is no longer available.";
        document.body.appendChild(warningMessage);
    }
})();
