do ($ = jQuery, window, document) ->
    greetings =
        production: """

**************************************
*     Application In Production!     *
**************************************

"""

    rpcAction = (endpoint, term, method, args) ->
        success = (response) ->
            term.resume()
            term.echo response.result

        error = (xhr, type, message) ->
            term.resume()
            term.error message

        term.pause()
        $.jrpc Terminal.endpoint.artisan, method, args, success, error

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

    commandParser = (command, term, search) ->
        cmd = $.terminal.parseCommand command.trim()
        if cmd.name is search
            endpoint = Terminal.endpoint[search]
            method = cmd.args.shift()
            params = cmd.args
            if (search is "artisan" and Terminal.environment is "production" and $.inArray("--force", params) is -1) and (
                (starts_with(method, "migrate") is true and starts_with(method, "migrate:status") is false) or
                starts_with(method, "db:seed") is true
            )
                terminalConfirm term, greetings.production, "Do you really wish to run this command? [y/N] (yes/no) [no]:"
                    .done (result) ->
                        if result is true
                            params.push "--force"
                            rpcAction endpoint, term, method, params
                        else
                            term.echo "\nCommand Cancelled!"
            else
                rpcAction endpoint, term, method, params
            return true
        return false

    $(document.body).terminal (command, term) ->
        if command is ""
            return
        unless commandParser command, term, "artisan"
            term.error "Command '#{command}' Not Found!"
        return
    ,
        onBlur: ->
            false
