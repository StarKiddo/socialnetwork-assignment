# Popis práce
Tak nějak jsem to všecko (i když na poslední chvíli) zvládnul.. Až na to přidávání přes Ajax.. Na to už jsem neměl. Asi by se to dalo udělat ještě líp, a ten kód by chtěl pročistit. Nicméně i když je to sprasený, tak je to funkční. Aspoň doufám.. Nestihl jsem to pořádně otestovat..

Liky jsem vyřešil tak, že mám v databázi tabulku, do který zapisuju id příspěvku a usera, ktarý likuje. Unlike je mazání záznamu v této databázi.

V tabulce users jsou základní informace o uživateli. email, hash hesla, jméno, příjmení a cesty k obrázkům banneru a profilovky + about, čož je část něco o mě. Vytvořil jsem zvláštní stránku na úpravu těchto informací.

V tabulce stuff jsou příspěvky ukládá se sem id uživatele, který příspěvek vytvořil, datum přidání příspěvku, obsah příspěvku, id uživatele, který případně příspěvek sdílí, a pokud je sdílen obrázek, nebo youtube video, tak se zde ukládá i adresa obrázku, nebo thumbnailu youtube videa. Kvůli sdílení ve skupinách jsem přidal také sloupec grouId, do kterého se ukládá id skupiny, pokud je příspěvěk vytvořen ve skupině.

Sdílení příspěvků jsem vyřešil tak, že pokud někdo příspěvek sdílí, vytvoří se nový záznam v tabulce s příspěvky a do sloupce shareId se napíše id sdílejícího usera.

Tabulka members obsahuje záznamy o přidání se ke skupinám. Ve sloupci groupId je id skupiny, ke které se dotyčný přidává a ve sloupci memberId je id uživatele, který se ke spuině přidal. Pokud ze skupiny dotyčný odejde, jeho záznam se z tabulky smaže.

Tabulka groups obsahuje informace o skupinách. Sloupec name je pro název skupiny, picPath je pro obrázek skupiny, description je popis skupiny. 

Tabulka friends je pro navazování přátelství :). Vždy se vytvoří, nebo smažou dva záznamy, kvůli jednodušší práci při zjišťování, zda jsou dotyční přátelé, či nikoli. Měl jsem v plánu ještě udělat notifikace o žádostech o přátelství, ale bohužel nezbyl čas :/, takže potvrzování neprobíhá a pokud jeden přátelství naváže, jsou oba přáteli, ale ten druhý může záznamy smazat.


Petr Hasman
