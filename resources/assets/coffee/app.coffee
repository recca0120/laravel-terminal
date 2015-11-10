do ($ = jQuery, window, document) ->
    commandParser = (command, prefix) ->
        length = prefix.length
        if (command.indexOf(prefix) is 0 or command.indexOf(prefix) is length)
            params = command.replace(new RegExp("^#{prefix} ?"), "").split(" ")
            method = params.shift()
            term.pause()
            $.jrpc Terminal.endpoint.artisan, options.method, options.params, (response) ->
                term.resume()
                term.echo response.result
            true
        return false

    $(document.body).terminal (command, term) ->
        unless commandParser command, term
            term.error "Command '#{command}' Not Found!"
        return
    ,
        onBlur: ->
            false
