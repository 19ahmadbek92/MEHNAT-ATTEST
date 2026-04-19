#!/usr/bin/env bash
# nginx.sh bu katalogni "source" qiladi — exit emas, return.
# PHP-FPM LISTEN bo'lguncha kutamiz (Render HEAD / → fastcgi 127.0.0.1:9000 refused).

wait_for_upstream_php() {
    local i=0
    local max=240
    local sock="${WEB_PHP_SOCKET:-127.0.0.1:9000}"
    if [[ -z "${sock// }" ]]; then
        sock='127.0.0.1:9000'
    fi

    if [[ "$sock" == unix:* ]]; then
        local path="${sock#unix:}"
        while ((i < max)); do
            [[ -S "$path" ]] && return 0
            sleep 0.05
            ((i++)) || true
        done
        echo "laravel: unix socket kutish timeout: ${path}" >&2
        return 1
    fi

    if [[ "$sock" =~ ^[0-9.]+:[0-9]+$ ]]; then
        local host="${sock%:*}"
        local port="${sock#*:}"
        while ((i < max)); do
            if command -v ss >/dev/null 2>&1; then
                if ss -ltn 2>/dev/null | grep -qE ":${port}\\b"; then
                    return 0
                fi
            elif command -v nc >/dev/null 2>&1; then
                if nc -z "$host" "$port" 2>/dev/null; then
                    return 0
                fi
            elif bash -c "exec 3<>/dev/tcp/${host}/${port}" 2>/dev/null; then
                exec 3<&- 3>&- 2>/dev/null || true
                return 0
            fi
            sleep 0.05
            ((i++)) || true
        done
        echo "laravel: TCP ${host}:${port} kutish timeout" >&2
        return 1
    fi

    echo "laravel: WEB_PHP_SOCKET tushunarsiz (kutilgan host:port yoki unix:): ${sock}" >&2
    return 1
}

wait_for_upstream_php
