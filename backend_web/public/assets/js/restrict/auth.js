const auth = {
    id_user: "",
    id_owner: "",
    id_profile: "",
    readable: 0,
    writable: 0,

    PROFILES: {
        ROOT: 1,
        SYS_ADMIN: 2,
        BUSINESS_OWNER: 3,
        BUSINESS_MANAGER: 4,
    },

    is_root: idprofile => !idprofile ?auth.id_profile === auth.PROFILES.ROOT : auth.PROFILES.ROOT === idprofile,

    is_sysadmin: idprofile => !idprofile ? auth.id_profile === auth.PROFILES.SYS_ADMIN : auth.PROFILES.SYS_ADMIN === idprofile,

    is_business_owner: idprofile => !idprofile ? auth.id_profile === auth.PROFILES.BUSINESS_OWNER : auth.PROFILES.BUSINESS_OWNER === idprofile,

    is_business_manager: idprofile => !idprofile ? auth.id_profile === auth.PROFILES.BUSINESS_MANAGER : auth.PROFILES.BUSINESS_MANAGER === idprofile,

    is_business: idprofile => !idprofile
        ? [auth.PROFILES.BUSINESS_OWNER, auth.PROFILES.BUSINESS_MANAGER].includes(auth.id_profile)
        : [auth.PROFILES.BUSINESS_OWNER, auth.PROFILES.BUSINESS_MANAGER].includes(idprofile),

    is_system: idprofile => !idprofile
        ? [auth.PROFILES.ROOT, auth.PROFILES.SYS_ADMIN].includes(auth.id_profile)
        : [auth.PROFILES.ROOT, auth.PROFILES.SYS_ADMIN].includes(idprofile),

    can_read: () => auth.readable===1,
    can_write: () => auth.writable===1,

    have_sameowner: idowner => auth.id_owner === idowner,
}

export default auth