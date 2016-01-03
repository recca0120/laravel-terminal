do ($ = jQuery, window, document) ->
    outputFormater = (str, color) ->
        str = str.replace("[", "&#91").replace("]", "&#93")
        "[[;#{color};]#{str}]"

    info = (str) ->
        outputFormater str, "#008400"

    comment = (str) ->
        outputFormater str, "#a50"

    greetings =
        production: comment("""
**************************************
*     Application In Production!     *
**************************************
""")

    rpcAction = (endpoint, term, method, args) ->
        success = (response) ->
            term.resume()
            if response.result is false
                term.error response.error
            else
                term.echo response.result

        error = (xhr, type, message) ->
            term.resume()
            term.error message

        term.pause()
        $.jrpc endpoint, method, args, success, error

    terminalConfirm = do ->
        parseBoolean = (result) ->
            return switch (result.toLowerCase())
                when 'y', 'yes' then true
                else false

        (term, message = "", prompt = ">") ->
            defer = $.Deferred()

            if message isnt ""
                term.echo message

            term.push (command) ->
                defer.resolve parseBoolean(command)
                term.pop()
                return
            ,
                prompt: prompt
            defer.promise()

    starts_with = (str, search) ->
        return str.indexOf(search) is 0

    interpreter = (command, term, search, callback =(() ->), prompt) ->
        if command is search
            unless prompt
                prompt = search
            term.push (command) ->
                callback(prompt, command, term)
                return
            ,
                prompt: "#{prompt}>"
            return true
        return false

    execute = (command, term, search) ->
        cmd = $.terminal.parseCommand command.trim()
        if cmd.name is search
            endpoint = Terminal.endpoint[search]
            params = cmd.args
            method = params.shift() || "list"
            if (search is "artisan" and Terminal.environment is "production" and $.inArray("--force", params) is -1) and (
                (starts_with(method, "migrate") is true and starts_with(method, "migrate:status") is false) or
                starts_with(method, "db:seed") is true
            )
                terminalConfirm term, "\n#{greetings.production}\n", "#{info('Do you really wish to run this command? [y/N] (yes/no)')} [#{comment('no')}]: "
                    .done (result) ->
                        if result is true
                            params.push "--force"
                            rpcAction endpoint, term, method, params
                        else
                            term.echo "\n#{comment('Command Cancelled!')}\n"
            else
                rpcAction endpoint, term, method, params
            return true
        return false

    $(document.body).terminal (command, term) ->
        if command is ""
            return

        if interpreter(command, term, "artisan tinker", (prompt, command, term) ->
            endpoint = Terminal.endpoint[prompt]
            rpcAction endpoint, term, command, []
        , "tinker") is true
            return

        else unless execute command, term, "artisan"
            term.error "Command '#{command}' Not Found!"
        return
    ,
        onBlur: ->
            false
