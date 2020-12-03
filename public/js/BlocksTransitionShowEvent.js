let screenWidth = screen.width;

class BlocksTransitionShowEvent {

    blocksApparition(blocks, width) {
    let sideBlocks = document.querySelectorAll('.' + blocks)
    if (document.querySelector('.event-description')) {
        let descriptionBlock = document.querySelector('.event-description')
        if (screenWidth > 600) { 
            descriptionBlock.style.transition = '0.45s'
            descriptionBlock.style.width = '60%'
            descriptionBlock.style.padding = '0 30px'
        } else {
            descriptionBlock.style.width = '100%'
        }

    }
    sideBlocks.forEach(sideBlock => {
        sideBlock.style.transition = '1s'
        sideBlock.style.width = width
        sideBlock.style.padding = '20px'    
    });
    }

}

let showEvent = new BlocksTransitionShowEvent;

if (document.querySelector('.event-side-block')) {
    showEvent.blocksApparition('event-side-block', '80%');
}
if (document.querySelector('.each-event')) {
    if (screenWidth > 600) {
        showEvent.blocksApparition('each-event', '600px');
    } else {
        showEvent.blocksApparition('each-event', '300px');
    }
}
