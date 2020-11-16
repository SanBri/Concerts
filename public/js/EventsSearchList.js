let query = document.querySelector('.query')
let searchBlock = document.querySelector('.search-event-block')
let searchBlockContent = document.querySelector('.search-event-block__content')


class EventsSearchList {

    reduceSearchBlock() {
        searchBlock.style.transition = '0.5s'
        searchBlock.style.height = '200px'
        searchBlockContent.style.transition = '0.5s'
        searchBlockContent.style.background = 'rgba(255, 255, 255, 0.4)'
    }

}

let eventSearch = new EventsSearchList;

if (query) {
    eventSearch.reduceSearchBlock();
}
