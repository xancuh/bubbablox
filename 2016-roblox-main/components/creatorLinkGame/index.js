import {itemNameToEncodedName} from "../../services/catalog";
import Link from "../link";

/**
 * Creator link
 * @param {{type: string | number; id: number; name: string;}} props 
 * @returns 
 */
const CreatorLinkGame = (props) => {
  const url = '/users/' + props.id + "/profile";
  return <Link href={url}>
    <a>
      {props.name}
    </a>
  </Link>
}

export default CreatorLinkGame;