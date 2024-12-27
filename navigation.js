const list = document.querySelectorAll('.list');
const indicator = document.querySelector('.indicator'); // Target the indicator

function activelink() {
    list.forEach((item) => item.classList.remove('active')); // Remove 'active' class from all items
    this.classList.add('active'); // Add 'active' class to clicked item

    // Move the indicator to the correct position based on the active item
    const index = Array.from(list).indexOf(this); // Get index of clicked item
    indicator.style.left = `${index * 50}px`; // Set the position of the indicator
}

list.forEach((item) => item.addEventListener('click', activelink));
