let buttons = document.querySelectorAll('.category-btn')

class StatusFilter {

    show(toHide, toShow) {
        toHide.forEach((button)=> {
            if ( button !== toShow) { // Empêche que le script s'exécute en cliquant plusieurs fois sur le même bouton
                button.className = 'category-btn btn'
                document.querySelector('.' + button.id).style.display = "none"
            }
        })
        if ( document.querySelector('.' + toShow.id).style.display !== "block" ) {
            toShow.className = 'category-btn btn currentBtn'
            document.querySelector('.' + toShow.id).style.display = "block"
        }   
    }
}

let showOnlyStatus = new StatusFilter;

if (buttons) {
    buttons.forEach((button)=>{
        button.addEventListener('click', ()=> {
            showOnlyStatus.show(buttons,button)
        })
    })
}