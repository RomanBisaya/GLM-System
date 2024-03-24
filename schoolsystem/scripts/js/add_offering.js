function addScheduleDetails() {
    const container = document.getElementById('scheduleDetailsContainer');
    const inputGroup = document.createElement('div');
    inputGroup.classList.add('form-group');

    const newScheduleInput = `<input type="text" name="scheduleDetails[]" class="form-control" placeholder="Enter schedule details" required>`;
    inputGroup.innerHTML = newScheduleInput;
    container.appendChild(inputGroup);
}
