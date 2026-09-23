// trails the cursor
let matrail = document.querySelector("#trailer");

function setupmatrail(){
    if(matrail != undefined){
        styles = ["background-color:white","aspect-ratio:1","width: 25px","border-radius: 15px"];
        matrail.setAttribute("style",styles.join(";"));
        let icon = document.createElement('i');
        matrail.innerHTML += icon.outerHTML;
    }
}

const updatetrail = (e,oversth) => {
    const x = e.clientX - (matrail.offsetWidth / 2),
        y = e.clientY - (matrail.offsetWidth / 2);

    matrail.style.transform = `translate(${x}px,${y}px) scale(${oversth ? 1.5 : 0.8})`;
    matrail.style.opacity = `(${oversth ? 0.5 : 1})`;
}

window.onmousemove = e => {
    let itemunder = e.target.closest("a"),
        interacting = itemunder != null;

    updatetrail(e,interacting);

    let icon = matrail.querySelector("i");

    icon.dataset.active = interacting ? "yes" : "no";
    if(interacting){
        icon.className = getmyclass(itemunder.dataset.role);
        matrail.classList.add("halfviz");
    } else {
        icon.className = "fa fa-circle";
        matrail.classList.remove("halfviz");
    }
};

function getmyclass(marole) {
    switch (marole) {
        case "goright":
            return "fa fa-arrow-right";
        case "goleft":
            return "fa fa-arrow-left";
        case "control":
            return "fa fa-circle-o";
        case "scrub":
            return "fa fa-i-cursor";
        default:
            return "fa fa-hand-o-up"
    }
}

setupmatrail();