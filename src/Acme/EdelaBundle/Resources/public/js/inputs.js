function initFocus()
{
    var inputs = document.getElementsByTagName("input");
    var labels = document.getElementsByTagName("label");
    for (var i=0; i<inputs.length; i++)
    {
        if (inputs[i].type == "radio" || inputs[i].type == "checkbox")
        {
            inputs[i].onclick = function ()
            {
                for (var j=0; j<labels.length; j++)
                {
                    if(this.id == labels[j].htmlFor)
                    {
                        if(labels[j].className.indexOf("focus") == -1)
                        {
                            labels[j].className += " focus";
                        }
                        else if(this.type != "radio")
                        {
                            labels[j].className = labels[j].className.replace("focus", "");
                        }
                    }
                    else if(this.type == "radio" && this.name == document.getElementById(labels[j].htmlFor).name)
                    {
                        labels[j].className = labels[j].className.replace("focus", "");
                    }
                }
            }
            if(inputs[i].checked == true)
            {
                for (var j=0; j<labels.length; j++)
                {
                    if(inputs[i].id == labels[j].htmlFor)
                        labels[j].className += " focus";
                }
            }
        }
    }
}
if (window.addEventListener)
    window.addEventListener("load", initFocus, false);
else if (window.attachEvent)
    window.attachEvent("onload", initFocus);
