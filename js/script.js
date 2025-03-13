'use strict';
let avtoriz=document.getElementById('avtoriz');
let modal=document.getElementById('modal');

avtoriz.addEventListener('click', (event) => {
    event.preventDefault(); // Prevent the link from navigating
    let myModal = new bootstrap.Modal(modal);
    myModal.show();
});