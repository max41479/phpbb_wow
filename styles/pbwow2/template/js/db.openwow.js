if (typeof $utilGrp == "undefined") {
    var $utilGrp = new function() {
        function t(AS, AR) {
            var AQ = document.createElement(AS);
            if (AR) {
                AK(AQ, AR)
                }
            return AQ
        }
        function O(AQ, AR) {
            return AQ.appendChild(AR)
            }
        function s(AR, AS, AQ) {
            if (window.attachEvent) {
                AR.attachEvent("on" + AS, AQ)
                } else {
                AR.addEventListener(AS, AQ, false)
                }
        }
        function AK(AS, AQ) {
            for (var AR in AQ) {
                if (typeof AQ[AR] == "object") {
                    if (!AS[AR]) {
                        AS[AR] = {}
                    }
                    AK(AS[AR], AQ[AR])
                    } else {
                    AS[AR] = AQ[AR]
                    }
            }
        }
        function l(AQ) {
            if (!AQ) {
                AQ = event
            }
            if (!AQ._button) {
                AQ._button = AQ.which ? AQ.which: AQ.button;
                AQ._target = AQ.target ? AQ.target: AQ.srcElement
            }
            return AQ
        }
        function AB() {
            var AR = 0,
            AQ = 0;
            if (typeof window.innerWidth == "number") {
                AR = window.innerWidth;
                AQ = window.innerHeight
            } else {
                if (document.documentElement && (document.documentElement.clientWidth || document.documentElement.clientHeight)) {
                    AR = document.documentElement.clientWidth;
                    AQ = document.documentElement.clientHeight
                } else {
                    if (document.body && (document.body.clientWidth || document.body.clientHeight)) {
                        AR = document.body.clientWidth;
                        AQ = document.body.clientHeight
                    }
                }
            }
            return {
                w: AR,
                h: AQ
            }
        }
        function b() {
            var AQ = 0,
            AR = 0;
            if (typeof(window.pageYOffset) == "number") {
                AQ = window.pageXOffset;
                AR = window.pageYOffset
            } else {
                if (document.body && (document.body.scrollLeft || document.body.scrollTop)) {
                    AQ = document.body.scrollLeft;
                    AR = document.body.scrollTop
                } else {
                    if (document.documentElement && (document.documentElement.scrollLeft || document.documentElement.scrollTop)) {
                        AQ = document.documentElement.scrollLeft;
                        AR = document.documentElement.scrollTop
                    }
                }
            }
            return {
                x: AQ,
                y: AR
            }
        }
        function AH(AS) {
            var AR,
            AT;
            if (window.innerHeight) {
                AR = AS.pageX;
                AT = AS.pageY
            } else {
                var AQ = b();
                AR = AS.clientX + AQ.x;
                AT = AS.clientY + AQ.y
            }
            return {
                x: AR,
                y: AT
            }
        }
        function c(AR) {
            var AQ = c.L;
            return (AQ[AR] ? AQ[AR] : 0)
            }
        c.L = {
            fr: 2,
            de: 3,
            es: 6,
            ru: 7,
            wotlk: 0,
            ptr: 0
        };
        function AF(AQ) {
            var AR = AF.L;
            return (AR[AQ] ? AR[AQ] : -1)
            }
        AF.L = {
            npc: 1,
            object: 2,
            item: 3,
            itemset: 4,
            quest: 5,
            spell: 6,
            zone: 7,
            faction: 8,
            pet: 9,
            achievement: 10
        };
        function o(AV, AR, AU) {
            var AT = {
                12: 1.5,
                13: 12,
                14: 15,
                15: 5,
                16: 10,
                17: 10,
                18: 8,
                19: 14,
                20: 14,
                21: 14,
                22: 10,
                23: 10,
                24: 0,
                25: 0,
                26: 0,
                27: 0,
                28: 10,
                29: 10,
                30: 10,
                31: 10,
                32: 14,
                33: 0,
                34: 0,
                35: 25,
                36: 10,
                37: 2.5,
                44: 4.69512176513672
            };
            if (AV < 0) {
                AV = 1
            } else {
                if (AV > 80) {
                    AV = 80
                }
            }
            if ((AR == 14 || AR == 12 || AR == 15) && AV < 34) {
                AV = 34
            }
            if (AU < 0) {
                AU = 0
            }
            var AS;
            if (AT[AR] == null) {
                AS = 0
            } else {
                var AQ;
                if (AV > 70) {
                    AQ = (82 / 52) * Math.pow((131 / 63), ((AV - 70) / 10))
                    } else {
                    if (AV > 60) {
                        AQ = (82 / (262 - 3 * AV))
                        } else {
                        if (AV > 10) {
                            AQ = ((AV - 8) / 52)
                            } else {
                            AQ = 2 / 52
                        }
                    }
                }
                AS = AU / AT[AR] / AQ
            }
            return AS
        }
        var a = {
            applyto: 3
        },
        J,
        y,
        AG,
        r,
        W,
        w,
        u,
        R = document.getElementsByTagName("head")[0],
        e = {},
        Z = {},
        K = {},
        AO = {},
        x,
        Y,
        C,
        f,
        AI,
        F = 1,
        n = 0,
        z = !!(window.attachEvent && !window.opera),
        T = navigator.userAgent.indexOf("MSIE 7.0") != -1,
        V = navigator.userAgent.indexOf("MSIE 6.0") != -1 && !T,
        i = {
            loading: "Loading...",
            noresponse: "No response"
        },
        AD = 0,
        N = 1,
        L = 2,
        q = 3,
        AC = 4,
        h = 3,
        p = 5,
        X = 6,
        AA = 10,
        Q = 15,
        k = 15,
        S = {
            3: [e, "item", "Item"],
            5: [Z, "quest", "Quest"],
            6: [K, "spell", "Spell"],
            10: [AO, "achievement", "Achievement"]
            },
        H = {
            0: "enus",
            2: "frfr",
            3: "dede",
            6: "eses",
            7: "ruru",
            25: "ptr"
        };
        function AM() {
            O(R, t("link", {
                type: "text/css",
                href: "//cdn.openwow.com/api/tooltip.css?3",
                rel: "stylesheet"
            }));
            s(document, "mouseover", g)
            }
        function P(AQ) {
            var AR = AH(AQ);
            w = AR.x;
            u = AR.y
        }
        function AN(Aa, AY) {
            if (Aa.nodeName != "A" && Aa.nodeName != "AREA") {
                return - 2323
            }
            if (!Aa.href.length) {
                return
            }
            var AW,
            AU,
            AS,
            AR,
            AT = {},
			AB;
            var AQ = function(Ab, Ad, Ac) {};
            if (a.applyto & 1) {
                AW = 1;
                AU = 2;
                AS = 3;
                AR = Aa.href.match(/^http:\/\/(www|wotlk|cata)?\.?openwow\.com\/.?(item|quest|spell|achievement|npc|object)=([0-9]+)/);
				AB = Aa.href;
                n = 0
            }
            if (AR == null && (a.applyto & 2) && Aa.rel) {
                AW = 0;
                AU = 1;
                AS = 2;
                AR = Aa.rel.match(/(item|quest|spell|achievement|npc|object).?([0-9]+)/);
				AB = Aa.href;
                n = 1
            }
            if (Aa.rel) {
                Aa.rel.replace(/([a-zA-Z]+)=?([a-zA-Z0-9:-]*)/g, AQ);
                if (AT.gems && AT.gems.length > 0) {
                    var AX;
                    for (AX = Math.min(3, AT.gems.length - 1); AX >= 0;--AX) {
                        if (parseInt(AT.gems[AX])) {
                            break
                        }
                    }++AX;
                    if (AX == 0) {
                        delete AT.gems
                    } else {
                        if (AX < AT.gems.length) {
                            AT.gems = AT.gems.slice(0, AX)
                            }
                    }
                }
            }
            if (AR) {
                var AZ,
                AV = "www";
                if (AT.domain) {
                    AV = AT.domain
                } else {
                    if (AW && AR[AW]) {
                        AV = AR[AW]
                        }
                }
                AZ = c(AV);
                r = AV;
                if (!Aa.onmousemove) {
                    Aa.onmousemove = B;
                    Aa.onmouseout = D
                }
                P(AY);
                j(AF(AR[AU]), AR[AS], AZ, AT, AB)
                }
        }
        function g(AS) {
            AS = l(AS);
            var AR = AS._target;
            var AQ = 0;
            while (AR != null && AQ < 3 && AN(AR, AS) == -2323) {
                AR = AR.parentNode;++AQ
            }
        }
        function B(AQ) {
            AQ = l(AQ);
            P(AQ);
            d()
            }
        function D() {
            J = null;
            U()
            }
        function G() {
            if (!x) {
                var AV = t("div"),
                AZ = t("table"),
                AS = t("tbody"),
                AU = t("tr"),
                AR = t("tr"),
                AQ = t("td"),
                AY = t("th"),
                AX = t("th"),
                AW = t("th");
                AV.className = "openwow-tooltip";
                AY.style.backgroundPosition = "top right";
                AX.style.backgroundPosition = "bottom left";
                AW.style.backgroundPosition = "bottom right";
                O(AU, AQ);
                O(AU, AY);
                O(AS, AU);
                O(AR, AX);
                O(AR, AW);
                O(AS, AR);
                O(AZ, AS);
                f = t("p");
                f.style.display = "none";
                O(f, t("div"));
                O(AV, f);
                O(AV, AZ);
                O(document.body, AV);
                x = AV;
                Y = AZ;
                C = AQ;
                var AT = t("div");
                AT.className = "openwow-tooltip";
                O(AV, AT);
                AI = AT;
                U()
                }
        }
        function AP(AS, AT) {
            var AU = false;
            if (!x) {
                G()
                }
            if (!AS) {
                AS = S[J][2] + " does not exist";
                AT = "inv_misc_questionmark";
                AU = true
            } else {
                if (W.pcs && W.pcs.length) {
                    var AV = 0;
                    for (var AR = 0, AQ = W.pcs.length; AR < AQ;++AR) {
                        if (m = AS.match(new RegExp("<span><!--si([0-9]+:)*" + W.pcs[AR] + "(:[0-9]+)*-->"))) {
                            AS = AS.replace(m[0], '<span class="q8"><!--si' + W.pcs[AR] + "-->");++AV
                        }
                    }
                    if (AV > 0) {
                        AS = AS.replace("(0/", "(" + AV + "/");
                        AS = AS.replace(new RegExp("<span>\\(([0-" + AV + "])\\)", "g"), '<span class="q2">($1)')
                        }
                }
                if (W.c) {
                    AS = AS.replace(/<span class="c([0-9]+?)">(.+?)<\/span><br \/>/g, '<span class="c$1" style="display: none">$2</span>');
                    AS = AS.replace(new RegExp('<span class="c(' + W.c + ')" style="display: none">(.+?)</span>', "g"), '<span class="c$1">$2</span><br />')
                    }
                if (W.lvl) {
                    AS = AS.replace(/\(<!--r([0-9]+):([0-9]+):([0-9]+)-->([0-9.%]+)(.+?)([0-9]+)\)/g, function(AX, AX, AY, AW, AX, Aa, AX) {
                        var AZ = o(W.lvl, AY, AW);
                        AZ = (Math.round(AZ * 100) / 100);
                        if (AY != 12 && AY != 37) {
                            AZ += "%"
                        }
                        return "(<!--r" + W.lvl + ":" + AY + ":" + AW + "-->" + AZ + Aa + W.lvl + ")"
                    })
                    }
            }
            if (AI) {
                AI.style.display = (n && !AU ? "": "none")
                }
            if (F && AT) {
                f.style.backgroundImage = "url(//cdn.openwow.com/images/icons/medium/" + AT.toLowerCase() + ".jpg)";
                f.style.display = ""
            } else {
                f.style.backgroundImage = "none";
                f.style.display = "none"
            }
            x.style.display = "";
            x.style.width = "320px";
            C.innerHTML = AS;
            AL();
            d();
            x.style.visibility = "visible"
        }
        function U() {
            if (!x) {
                return
            }
            x.style.display = "none";
            x.style.visibility = "hidden"
        }
        function AL() {
            var AR = C.childNodes;
            if (AR.length >= 2 && AR[0].nodeName == "TABLE" && AR[1].nodeName == "TABLE") {
                AR[0].style.whiteSpace = "nowrap";
                var AQ;
                if (AR[1].offsetWidth > 300) {
                    AQ = Math.max(300, AR[0].offsetWidth) + 20
                } else {
                    AQ = Math.max(AR[0].offsetWidth, AR[1].offsetWidth) + 20
                }
                if (AQ > 20) {
                    x.style.width = AQ + "px";
                    AR[0].style.width = AR[1].style.width = "100%"
                }
            } else {
                x.style.width = Y.offsetWidth + "px"
            }
        }
        function d() {
            if (!x) {
                return
            }
            if (w == null) {
                return
            }
            var AZ = AB(),
            Aa = b(),
            AW = AZ.w,
            AT = AZ.h,
            AV = Aa.x,
            AS = Aa.y,
            AU = Y.offsetWidth,
            AQ = Y.offsetHeight,
            AR = w + Q,
            AY = u - AQ - k;
            if (AR + Q + AU + 4 >= AV + AW) {
                var AX = w - AU - Q;
                if (AX >= 0) {
                    AR = AX
                } else {
                    AR = AV + AW - AU - Q - 4
                }
            }
            if (AY < AS) {
                AY = u + k;
                if (AY + AQ > AS + AT) {
                    AY = AS + AT - AQ;
                    if (F) {
                        if (w >= AR - 48 && w <= AR && u >= AY - 4 && u <= AY + 48) {
                            AY -= 48 - (u - AY)
                            }
                    }
                }
            }
            x.style.left = AR + 20 + "px";
            x.style.top = AY + 20 + "px"
        }
        function AJ(AQ) {
            return (W && W.buff ? "buff_": "tooltip_") + H[AQ]
            }
        function AE(AS, AT, AR) {
            var AQ = S[AS][0];
            if (AQ[AT] == null) {
                AQ[AT] = {}
            }
            if (AQ[AT].status == null) {
                AQ[AT].status = {}
            }
            if (AQ[AT].status[AR] == null) {
                AQ[AT].status[AR] = AD
            }
        }
        function j(AT, AV, AR, AU, AB) {
            if (!AU) {
                AU = {}
            }
            var AS = I(AV, AU);
            J = AT;
            y = AS;
            AG = AR;
            W = AU;
            AE(AT, AS, AR);
            var AQ = S[AT][0];
            if (AQ[AS].status[AR] == AC || AQ[AS].status[AR] == q) {
                AP(AQ[AS][AJ(AR)], AQ[AS].icon)
                } else {
                if (AQ[AS].status[AR] == N) {
                    AP(i.tooltip_loading)
                    } else {
                    E(AT, AV, AR, null, AU, AB)
                    }
            }
        }
        function E(AW, AQ, AY, AV, AS, AB) {
            var AX = I(AQ, AS);
            var AU = S[AW][0];
            if (AU[AX].status[AY] != AD && AU[AX].status[AY] != L) {}
            AU[AX].status[AY] = N;
            if (!AV) {
                AU[AX].timer = setTimeout(function() {
                    M.apply(this, [AW, AX, AY])
                    }, 333)
                }
            var AR = "";
            for (var AT in AS) {
                if (AT != "rand" && AT != "ench" && AT != "gems" && AT != "sock") {
                    continue
                }
                if (typeof AS[AT] == "object") {
                    AR += "&" + AT + "=" + AS[AT].join(":")
                    } else {
                    if (AT == "sock") {
                        AR += "&sock"
                    } else {
                        AR += "&" + AT + "=" + AS[AT]
                        }
                }
            }
			var full = AB.match(/(?:http[s]*\:\/\/)*(.*?)\.(?=[^\/]*\..{2,5})/i);
			var su = full[1];
			if(su == "www"){
				su = "wotlk";
			}
            A("//"+su+".openwow.com/" + S[AW][1] + "=" + AQ + AR + "&power=true")
            }
			
		function log(msg) { 
			setTimeout(function() { 
				throw new Error(msg); 
			}, 0); 
		} 
	
        function A(AQ) {
            O(R, t("script", {
                type: "text/javascript",
                src: AQ
            }))
            }
        function M(AS, AT, AR) {
            if (J == AS && y == AT && AG == AR) {
                AP(i.loading);
                var AQ = S[AS][0];
                AQ[AT].timer = setTimeout(function() {
                    v.apply(this, [AS, AT, AR])
                    }, 3850)
                }
        }
        function v(AS, AT, AR) {
            var AQ = S[AS][0];
            AQ[AT].status[AR] = L;
            if (J == AS && y == AT && AG == AR) {
                AP(i.tooltip_noresponse)
                }
        }
        function I(AR, AQ) {
            return AR + (AQ.rand ? "r" + AQ.rand: "") + (AQ.ench ? "e" + AQ.ench: "") + (AQ.gems ? "g" + AQ.gems.join(",") : "") + (AQ.sock ? "s": "")
            }
        this.register = function(AT, AU, AR, AS) {
            var AQ = S[AT][0];
            clearTimeout(AQ[AU].timer);
            AK(AQ[AU], AS);
            if (AQ[AU][AJ(AR)]) {
                AQ[AU].status[AR] = AC
            } else {
                AQ[AU].status[AR] = q
            }
            if (J == AT && AU == y && AG == AR) {
                AP(AQ[AU][AJ(AR)], AQ[AU].icon)
                }
        };
        this.regItem = function(AS, AQ, AR) {
            this.register(h, AS, AQ, AR)
            };
        this.regQuest = function(AS, AQ, AR) {
            this.register(p, AS, AQ, AR)
            };
        this.regSpell = function(AS, AQ, AR) {
            this.register(X, AS, AQ, AR)
            };
        this.regAchievement = function(AS, AQ, AR) {
            this.register(AA, AS, AQ, AR)
            };
        this.set = function(AQ) {
            AK(a, AQ)
            };
        this.showTooltip = function(AS, AR, AQ) {
            P(AS);
            AP(AR, AQ)
            };
        this.hideTooltip = function() {
            U()
            };
        this.moveTooltip = function(AQ) {
            B(AQ)
            };
        AM()
        }
};
