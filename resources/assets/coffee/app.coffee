do ($ = jQuery, window, document) ->
    csrfToken = $ "meta[name='csrf-token']"
        .attr "content"

    $.ajaxSetup
        headers:
            'X-CSRF-TOKEN': csrfToken

    outputFormater = (str, color) ->
        str = str
            .replace /&/g, "&amp;"
            .replace /\[/g, "&#91;"
            .replace /\]/g, "&#93;"
        "[[;#{color};]#{str}]"

    info = (str) ->
        outputFormater str, "#008400"

    comment = (str) ->
        outputFormater str, "#a50"

    environment = window.Terminal.environment
    endPoint = window.Terminal.endPoint
    copyright = [
        " __                        _    _____              _         _ "
        "|  |   ___ ___ ___ _ _ ___| |  |_   ____ ___ _____|_|___ ___| |"
        "|  |__| .'|  _| .'| | | -_| |    | || -_|  _|     | |   | .'| |"
        "|_____|__,|_| |__,|\\_/|___|_|    |_||___|_| |_|_|_|_|_|_|__,|_|"
        ""
        "Copyright (c) 2015 Recca Tsai <http://phpwrite.blogspot.tw/>"
        ""
        ""
        ""
        "Type a command, or type `#{info('help')}`, for a list of commands."
        ""
        ""
    ].join "\n"

    Loading = do ->
        anim = ["/", "|", "\\", "-"]
        intervalId = null
        delay = 50
        defaultPrompt = null
        show: (term) ->
            term.disable()
            defaultPrompt = term.get_prompt()
            i = 0
            intervalId = setInterval ->
                term.set_prompt anim[i++]
                if i > anim.length-1
                    i = 0
            , delay
        hide: (term) ->
            clearInterval intervalId
            term.enable()
            term.set_prompt defaultPrompt


    class Term
        ids: {},
        toBoolean: (result) =>
            return switch (result.toLowerCase())
                when 'y', 'yes' then true
                else false
        confirm: (term, message, title) =>
            deferred = $.Deferred()
            term.echo title
            term.echo message
            term.push (result) =>
                deferred.resolve @toBoolean result
                term.pop()
                return
            ,
                prompt: ">"
            deferred.promise()
        interpreter: (commandPrefix, term, prompt) =>
            unless prompt
                prompt = commandPrefix
            term.push (command) =>
                @execute term, "#{commandPrefix.replace(/\s+/g, '-')} #{command}"
                return false
            ,
                prompt: "#{prompt}>"
            return false
        rpcRequest: (term, cmd) =>
            @ids[cmd.method] = @ids[cmd.method] || 0;
            Loading.show term
            $.ajax
                url: endPoint,
                dataType: 'json'
                type: 'post'
                data:
                    jsonrpc: "2.0"
                    id: ++@ids[cmd.method]
                    cmd: cmd
            .success (response) =>
                term.echo response.result
            .error (jqXhr, json, errorThrown) ->
                term.error "#{jqXhr.status}: #{errorThrown}"
            .complete ->
                Loading.hide term

        capitalize = (str) =>
            "#{str.charAt(0).toUpperCase()}#{str.slice(1)}"

        commandArtisan: (term, cmd) =>
            cmd2 = $.terminal.parseCommand cmd.rest.trim()
            title = [
                ""
                comment("**************************************")
                comment("*     Application In Production!     *")
                comment("**************************************")
                ""
            ].join "\n"
            message = "#{info('Do you really wish to run this command? [y/N] (yes/no)')} #{comment('[no]')}: "

            if environment is "production" and $.inArray("--force", cmd2.args) is -1
                if (cmd2.name.indexOf("migrate") is 0 and cmd2.name.indexOf("migrate:status") is -1) or cmd2.name.indexOf("db:seed") is 0
                    @confirm term, message, title
                        .done (result) =>
                            if result is true
                                @rpcRequest term, cmd
                            else
                                term.echo " "
                                term.echo "#{comment('Command Cancelled!')}"
                                term.echo " "
                    return
            @rpcRequest term, cmd

        execute: (term, command) =>
            switch command
                when "help"
                    cmd = $.terminal.parseCommand "list"
                    @rpcRequest term, cmd
                when "artisan tinker"
                    @interpreter command, term, "tinker"
                when "mysql"
                    @interpreter command, term
                else
                    cmd = $.terminal.parseCommand command.trim()
                    if @["command#{capitalize(cmd.name)}"]
                        @["command#{capitalize(cmd.name)}"](term, cmd)
                    else
                        @rpcRequest term, cmd
            return

    terminal = new Term
    $(document.body).terminal (command, term) ->
        terminal.execute(term, command)
    ,
        onInit: (term) ->
            terminal.execute term, 'list'
        onBlur: ->
            false
        greetings: copyright
