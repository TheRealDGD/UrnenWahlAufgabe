const xOffs = 20
const yOffs = 20

const boxW = 100
const boxH = 200
const boxDist = 20

var x = -1
var y = -1
var sphereCol = "rgb(255,255,255)"
var boxes
var startTime

function load(pText,p)
{
    const canvas = document.getElementById("canvas")

    if (canvas)
    {
        canvas.style.display = "none"
        canvas.addEventListener("click",click)

        boxes = [{x:xOffs,y:yOffs,w:boxW,h:boxH,fillstyle:"rgb(200,200,0)",text:pText,prob:p,name:"Urne A"},{x:xOffs+boxW+boxDist,y:yOffs,w:boxW,h:boxH,fillstyle:"rgb(0,200,200)",text:"?:?",prob:(Math.round(Math.random()*100)/100),name:"Urne B"}]
        draw()

        document.getElementById("ok").addEventListener("click",showTask);
        document.getElementById("li2").style.display = "none"
        document.getElementById("submit").style.display = "none"
        document.getElementById("result").style.display = "none"
    }
}

function showTask(event)
{
    document.getElementById("canvas").style.display = "block"
    document.getElementById("ok").style.display = "none"
    document.getElementById("li1").style.display = "none"
    document.getElementById("li2").style.display = "list-item"

    startTime = new Date()
}

function draw() {
    const canvas = document.getElementById("canvas")
    if (canvas.getContext) {
        var ctx = canvas.getContext('2d')

        for(i=0;i<2;i++)
        {
            ctx.fillStyle = boxes[i].fillstyle
            ctx.fillRect(boxes[i].x,boxes[i].y,boxes[i].w,boxes[i].h)

            ctx.fillStyle = "rgb(0,0,0)"
            ctx.textAlign="center"
            ctx.textBaseline = "middle"
            ctx.font="20px sans-serif"
            ctx.fillText(boxes[i].text,boxes[i].x+boxW/2,boxes[i].y+boxH/2)

            ctx.fillStyle = "rgb(0,0,0)"
            ctx.textAlign="center"
            ctx.textBaseline = "bottom"
            ctx.font="20px sans-serif"
            ctx.fillText(boxes[i].name,boxes[i].x+boxW/2,boxes[i].y+boxH)
        }

        if ((x >= 0) && (y >= 0))
        {
            ctx.beginPath()
            ctx.fillStyle = sphereCol
            ctx.arc(x,y,20,0,Math.PI * 2 ,true)
            ctx.fill()
        }
    } else 
    {
        alert("Dein Browser unterstützt das <canvas> Element nicht")
    }
}

function submitResults(col,box,p)
{
    endTime = new Date()

    document.getElementById("color").value = col
    document.getElementById("box").value = box+1
    document.getElementById("p").value = p
    document.getElementById("time").value = endTime-startTime

    document.getElementById("canvas").removeEventListener("click",click)


    document.getElementById("ok").style.display = "none"
    document.getElementById("submit").style.display = "block"

    document.getElementById("result").style.display = "block"
    document.getElementById("result").innerHTML = "Du hast eine " + col + "e Kugel aus Urne " + String.fromCharCode(box+65) + " gezogen."

    //document.getElementById("form").submit()
}

function click(event)
{
    const canvas = document.getElementById("canvas")
    const rect = canvas.getBoundingClientRect()

    x=event.offsetX
    y=event.offsetY

    box = -1

    for(i=0;i<2;i++)
    {
        if ((x >= boxes[i].x) && (x <= boxes[i].x+boxes[i].w) && (y >= boxes[i].y) && (y <= boxes[i].y+boxes[i].h))
        {
            box = i
            break
        }
    }

    if (box >= 0)
    {
        p = boxes[i].prob

        if (Math.random() <= p)
        {
            col = "rot"
            sphereCol = "rgb(255,0,0)"
        } else
        {
            col = "blau"
            sphereCol = "rgb(0,0,255)"
        }

        draw()
        submitResults(col,box,p)
    }
}
