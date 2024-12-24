import './bootstrap';

let bookings = document.querySelectorAll('.booking');
let cars = document.querySelectorAll('.car-link');
let rows = document.querySelectorAll('.row');


for(let i=0; i<cars.length; i++){
    cars[i].addEventListener('click', (e) => {
        e.preventDefault();
        let car_id = cars[i].getAttribute('car_id');
        let row = cars[i].parentElement.parentElement;

        rows.forEach((el) => el.classList.remove("text-blue-500"));
        row.classList.add("text-blue-500");

        for(let j=0; j<bookings.length; j++){
            if(car_id == bookings[j].getAttribute('car_id')){
                bookings[j].hidden = !bookings[j].hidden;
            }
            else{
                bookings[j].hidden = true;
            }
        }
    });
}