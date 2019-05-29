#!/usr/bin/python3
import bcrypt

work_factor = 10

def concat_str_list(l:list):
    if not isinstance(l, list) or len(l) < 1:
        return False

    concat = ""
    for s in l:
        if not isinstance(s, str):
            return False
        concat += s

    return concat

# creates a single password hash with mulitple passwords
def multipw_hash(passwords:list):
    pwstr = concat_str_list(passwords)
    if not pwstr:
        return False

    return bcrypt.hashpw(pwstr.encode('utf-8'), bcrypt.gensalt(work_factor))

# verify a multi-password
# note: order of the 'passwords' list is important
def multipw_verify(passwords:list, hash:bytes):
    if not isinstance(hash, bytes):
        return False

    pwstr = concat_str_list(passwords)
    if not pwstr:
        return False

    return bcrypt.checkpw(pwstr.encode('utf-8'), hash)
