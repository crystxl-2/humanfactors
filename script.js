document.addEventListener("DOMContentLoaded", () => {
    // Update date and time
    function updateDateTime() {
        const timeElement = document.getElementById("current-time");
        const dateElement = document.getElementById("current-date");
        const currentDate = new Date();
        timeElement.innerHTML = currentDate.toLocaleTimeString();
        dateElement.innerHTML = currentDate.toLocaleDateString();
    }
    updateDateTime();
    setInterval(updateDateTime, 1000); // Update every second

    // Shift Start and End Dialogs
    const shiftStartBtn = document.getElementById("shift-start-btn");
    const shiftStartDialog = document.getElementById("shift-start-dialog");
    const shiftStartMessage = document.getElementById("shift-start-message");
    const closeShiftStartDialogBtn = document.getElementById("close-shift-start-dialog");

    const shiftEndBtn = document.getElementById("shift-end-btn");
    const shiftEndDialog = document.getElementById("shift-end-dialog");
    const shiftEndMessage = document.getElementById("shift-end-message");
    const closeShiftEndDialogBtn = document.getElementById("close-shift-end-dialog");

    shiftStartBtn.addEventListener("click", () => {
        const currentTime = new Date().toLocaleTimeString();
        shiftStartMessage.innerText = `Shift started at ${currentTime}`;
        shiftStartDialog.showModal();
    });

    closeShiftStartDialogBtn.addEventListener("click", () => {
        shiftStartDialog.close();
    });

    shiftEndBtn.addEventListener("click", () => {
        const currentTime = new Date().toLocaleTimeString();
        shiftEndMessage.innerText = `Shift ended at ${currentTime}`;
        shiftEndDialog.showModal();
    });

    closeShiftEndDialogBtn.addEventListener("click", () => {
        shiftEndDialog.close();
    });
});
