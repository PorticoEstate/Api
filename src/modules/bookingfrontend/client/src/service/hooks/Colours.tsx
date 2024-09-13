import {useMemo} from "react";

const colors: string[] = [
    "#FF9999", // Lys rød
    "#D4FFBF", // Lys grønn
    "#99CCFF", // Lys blå
    "#FFF2CC", // Lys gul
    "#FF99CC", // Lys magenta
    "#CCF6F3", // Lys cyan
    "#FFBDA7", // Lys koral
    "#B8CC99", // Lys olivengrønn
    "#FFDAB9", // Lys oransje
    "#CCFFFF", // Lys aqua
    "#E8D3C1", // Lys brun
    "#E9FFCC", // Lys gul-grønn
    "#FF6666", // Medium rød
    "#A3FF7F", // Medium grønn
    "#6699FF", // Medium blå
    "#FFE680", // Medium gul
    "#FF66B2", // Medium magenta
    "#66D9E8", // Medium cyan
    "#FF7F50", // Medium koral
    "#7BA02D", // Medium olivengrønn
    "#FFA500", // Medium oransje
    "#00FFFF", // Medium aqua
    "#A52A2A", // Medium brun
    "#C2FF8C", // Medium gul-grønn
    "#CC3333", // Mørk rød
    "#66CC4D", // Mørk grønn
    "#3366CC", // Mørk blå
    "#CC3380", // Mørk magenta
    "#3393A3", // Mørk cyan
    "#CC5A32", // Mørk koral
    "#556B2F", // Mørk olivengrønn
    "#FF8C00", // Mørk oransje
    "#0099A4", // Mørk aqua
    "#7B241C", // Mørk brun
    "#99CC33", // Mørk gul-grønn
    "#CCB833"  // Mørk gul
];
export const useColours = (): Array<string> | undefined => {
    return useMemo(() => colors, []);
}
