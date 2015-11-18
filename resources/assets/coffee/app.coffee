do ($ = jQuery, window, document) ->
    csrfToken = $ "meta[name='csrf-token']"
        .attr "content"

    $.ajaxSetup
        headers:
            'X-CSRF-TOKEN': csrfToken

    outputFormater = (str, color) ->
        str = str.replace("[", "&#91;").replace("]", "&#93;")
        "[[;#{color};]#{str}]"

    info = (str) ->
        outputFormater str, "#008400"

    comment = (str) ->
        outputFormater str, "#a50"

    greetings =
        copyright: [
            " __                        _    _____              _         _ "
            "|  |   ___ ___ ___ _ _ ___| |  |_   ____ ___ _____|_|___ ___| |"
            "|  |__| .'|  _| .'| | | -_| |    | || -_|  _|     | |   | .'| |"
            "|_____|__,|_| |__,|\\_/|___|_|    |_||___|_| |_|_|_|_|_|_|__,|_|"
            ""
            "Copyright (c) 2015 Recca Tsai <http://phpwrite.blogspot.tw/>"
            ""
        ].join "\n"


        production: [
            ""
            comment("**************************************")
            comment("*     Application In Production!     *")
            comment("**************************************")
            ""
        ].join "\n"

    request = do ->
        ids = {}
        (url, term, method, params) ->
            ids[url] = ids[url] || 0;
            term.pause()
            $.ajax
                url: url,
                dataType: 'json'
                type: 'post'
                data:
                    jsonrpc: "2.0"
                    method: method
                    params: params
                    id: ++ids[url]
            .success (response) ->
                if response.result is false
                    term.error response.error
                else
                    term.echo response.result
            .error (xhr, type, message) ->
                term.error message
            .complete ->
                term.resume()

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
                terminalConfirm term, "#{greetings.production}", "#{info('Do you really wish to run this command? [y/N] (yes/no)')} [#{comment('no')}]: "
                    .done (result) ->
                        if result is true
                            params.push "--force"
                            request endpoint, term, method, params
                        else
                            term.echo "\n#{comment('Command Cancelled!')}"
            else
                request endpoint, term, method, params
            return true
        return false

    $(document.body).terminal (command, term) ->
        if command is ""
            return

        if interpreter(command, term, "mysql", (prompt, command, term) ->
            endpoint = Terminal.endpoint[prompt]
            request endpoint, term, command, []
        ) is true
            return
        else if interpreter(command, term, "artisan tinker", (prompt, command, term) ->
            endpoint = Terminal.endpoint[prompt]
            request endpoint, term, command, []
        , "tinker") is true
            return

        else unless execute command, term, "artisan"
            term.error "Command '#{command}' Not Found!"
        return
    ,
        greetings: greetings.copyright
        onBlur: ->
            false
