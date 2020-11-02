let comingEventsBlock = document.querySelector('.coming-events-block')
let canceledEventsBlock = document.querySelector('.canceled-events-block')
let passedEventsBlock = document.querySelector('.passed-events-block')
let comingEventsBtn = document.getElementById('coming-events-btn')
let canceledEventsBtn = document.getElementById('canceled-events-btn')
let passedEventsBtn = document.getElementById('passed-events-btn')

class MyEvents {
    

    showComingEvents() {
        comingEventsBlock.style.display = "block";
        canceledEventsBlock.style.display = "none";
        passedEventsBlock.style.display = "none";
        comingEventsBtn.className = "btn currentBtn";
        canceledEventsBtn.className = "btn";
        passedEventsBtn.className = "btn";
    }
    showCanceledEvents() {
        canceledEventsBlock.style.display = "block";
        comingEventsBlock.style.display = "none";
        passedEventsBlock.style.display = "none";
        canceledEventsBtn.className = "btn currentBtn";
        comingEventsBtn.className = "btn";
        passedEventsBtn.className = "btn";
    }
    showPassedEvents() {
        passedEventsBlock.style.display = "block";
        comingEventsBlock.style.display = "none";
        canceledEventsBlock.style.display = "none";
        passedEventsBtn.className = "btn currentBtn";
        comingEventsBtn.className = "btn";
        canceledEventsBtn.className = "btn";
    }
}

let showMyEvents = new MyEvents;
if (comingEventsBlock) {
    comingEventsBtn.addEventListener('click', () => {
        showMyEvents.showComingEvents()
    })
    canceledEventsBtn.addEventListener('click', () => {
        showMyEvents.showCanceledEvents()
    })
    passedEventsBtn.addEventListener('click', () => {
        showMyEvents.showPassedEvents()
    })
}