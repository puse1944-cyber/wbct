function loader_show(){
    $('#cover-spin').show(0);
    const myTimeout = setTimeout(loader_hide, 3000);
}

function loader_hide(){
    $('#cover-spin').hide(0);
}

function buscar_ahora(buscar){
    var parametros = {"buscar":buscar};
    if(buscar.length == 0)
    {
        $.ajax({
            data:parametros,
            type: 'POST',
            url: '/api/v1.1/core/cc.php',
            success: function(data){
                document.getElementById("credits").innerHTML = data;
            }
        });
    }
    else if(buscar.length == 6)
    {
        $.ajax({
            data:parametros,
            type: 'POST',
            url: '/api/v1.1/core/cc.php',
            success: function(data){
                document.getElementById("credits").innerHTML = data;
            }
        });
    }
    else
    {
        
    }
}